<?php

namespace Remorhaz\JSON\Path\Parser;

use function array_fill_keys;
use function array_merge;
use Remorhaz\JSON\Path\Iterator\Evaluator;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\LiteralArrayValueList;
use Remorhaz\JSON\Path\Iterator\LiteralScalarValue;
use Remorhaz\JSON\Path\Iterator\LiteralValueList;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherList;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictElementMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilter;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\NodeValueListInterface;
use Remorhaz\JSON\Path\Iterator\EvaluatedValueList;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    private $fetcher;

    private $rootValue;

    private $output;

    private $evaluator;

    private $astBuilder;

    public function __construct(
        NodeValueInterface $rootValue,
        Fetcher $fetcher,
        Evaluator $evaluator,
        QueryAstBuilderInterface $astBuilder
    ) {
        $this->rootValue = $rootValue;
        $this->fetcher = $fetcher;
        $this->evaluator = $evaluator;
        $this->astBuilder = $astBuilder;
    }

    /**
     * @return NodeValueInterface[]
     */
    public function getOutput(): array
    {
        if (!isset($this->output)) {
            throw new Exception\OutputNotFoundException();
        }
        return $this->output;
    }

    /**
     * @param Symbol $symbol
     * @param Token $token
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        $s = $symbol->getShortcut();
        $t = $token->getShortcut();
        switch ($symbol->getSymbolId()) {
            case SymbolType::T_NAME:
            case SymbolType::T_UNESCAPED:
            case SymbolType::T_REGEXP_MOD:
                $s['s.text'] = $t['text'];
                break;

            case SymbolType::T_INT:
                $s['s.int'] = intval($t['text']);
                break;
        }
    }

    /**
     * @param Production $production
     */
    public function applyProductionActions(Production $production): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}";
        switch ($hash) {
            case SymbolType::NT_ROOT . ".0":
                // [ 0:NT_JSON_PATH, 1:T_EOI ]
                $this->output = $this
                    ->asValueList($symbols[0]['s.value_list'])
                    ->getValues();

                $this
                    ->astBuilder
                    ->setOutput($symbols[0]['s.value_list_id']);
                break;

            case SymbolType::NT_JSON_PATH . ".0":
                // [ 0:NT_PATH ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_PATH . ".0":
                // [ 0:T_ROOT_ABSOLUTE, 1:NT_FILTER_LIST ]
            case SymbolType::NT_PATH . ".1":
                // [ 0:T_ROOT_RELATIVE, 1:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".0":
                // [ 0:T_LEFT_BRACKET, 1:T_RIGHT_BRACKET ]
                $header['s.value_list'] = $this
                    ->evaluator
                    ->aggregate(
                        $header['i.filter_name'],
                        $this->asValueList($header['i.value_list'])
                    );

                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->calculateAggregate(
                        $header['i.filter_name'],
                        $header['i.value_list_id']
                    );
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1":
                // [ 0:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0":
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
            case SymbolType::NT_DOT_FILTER . ".1":
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.0':
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOUBLE_DOT_FILTER . '.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".0":
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
            case SymbolType::NT_FILTER_LIST . ".1":
                // [ 0:T_DOUBLE_DOT, 1:NT_DOUBLE_DOT_FILTER ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[4]['s.value_list'];
                $header['s.value_list_id'] = $symbols[4]['s.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".3":
                // [ ]
                $header['s.value_list'] = $header['i.value_list'];
                $header['s.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0":
                // [ 0:NT_EXPR_GROUP, 1:NT_WS_OPT ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1":
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2":
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    new LiteralScalarValue($symbols[0]['s.int'])
                );

                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralScalar(
                        $header['i.value_list_id'],
                        $symbols[0]['s.int']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".3":
                // [ 0:NT_ARRAY, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralArrayValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    ...$symbols[0]['s.array_elements']
                );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralArray(
                        $header['i.value_list_id'],
                        ...$symbols[0]['s.array_element_ids']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".4":
                // [ 0:T_NULL, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    new LiteralScalarValue(null)
                );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralScalar(
                        $header['i.value_list_id'],
                        null
                    );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".5":
                // [ 0:T_TRUE, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    new LiteralScalarValue(true)
                );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralScalar(
                        $header['i.value_list_id'],
                        true
                    );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".6":
                // [ 0:T_FALSE, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    new LiteralScalarValue(false)
                );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralScalar(
                        $header['i.value_list_id'],
                        false
                    );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".7":
                // [ 0:NT_STRING, 1:NT_WS_OPT ]
                $header['s.value_list'] = new LiteralValueList(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap(),
                    new LiteralScalarValue($symbols[0]['s.text'])
                );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->populateLiteralScalar(
                        $header['i.value_list_id'],
                        $symbols[0]['s.text']
                    );
                break;

            case SymbolType::NT_INT . ".0":
                $header['s.int'] = -$symbols[1]['s.int'];
                break;

            case SymbolType::NT_INT . ".1":
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_INT_NEXT . ".0":
                // [ 0:NT_WS_OPT, 1:NT_INT_NEXT_LIST ]
                $header['s.int_lists'] = array_fill_keys(
                    $this
                        ->asValueList($header['i.value_list'])
                        ->getIndexMap()
                        ->getInnerIndice(),
                    $symbols[1]['s.int_list']
                );
                $header['s.int_lists_id'] = $this
                    ->astBuilder
                    ->populateIndexList(
                        $header['i.value_list_id'],
                        ...$symbols[1]['s.int_list']
                    );
                break;

            case SymbolType::NT_INT_NEXT . '.1':
                // [ 0:NT_INT_SLICE ]
                $header['s.int_lists'] = $symbols[0]['s.int_lists'];
                $header['s.int_lists_id'] = $symbols[0]['s.int_lists_id'];
                break;

            case SymbolType::NT_INT_SLICE . '.0':
                // [ 0:T_COLON, 1:NT_INT_OPT, 2:NT_INT_SLICE_STEP, 3:NT_WS_OPT ]
                $header['s.value_list'] = $header['i.value_list'];
                $header['s.value_list_id'] = $header['i.value_list_id'];
                $header['s.int_lists'] = $this
                    ->fetcher
                    ->fetchSliceIndice(
                        $this->asValueList($header['i.value_list']),
                        $header['i.int_start'],
                        $symbols[1]['s.int'],
                        $symbols[2]['s.int']
                    );
                $header['s.int_lists_id'] = $this
                    ->astBuilder
                    ->populateIndexSlice(
                        $header['i.value_list_id'],
                        $header['i.int_start'],
                        $symbols[1]['s.int'],
                        $symbols[2]['s.int']
                    );
                break;

            case SymbolType::NT_INT_OPT . '.0':
                // [ 0:NT_INT ]
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_INT_OPT . '.1':
                $header['s.int'] = null;
                break;

            case SymbolType::NT_INT_SLICE_STEP . '.0':
                // [ 0:T_COLON, 1:NT_INT_OPT ]
                $header['s.int'] = $symbols[1]['s.int'];
                break;

            case SymbolType::NT_INT_SLICE_STEP . '.1':
                // [ ]
                $header['s.int'] = null;
                break;

            case SymbolType::NT_INT_NEXT_LIST . ".0":
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_INT, 3:NT_WS_OPT, 4:NT_INT_NEXT_LIST ]
                $header['s.int_list'] = $symbols[4]['s.int_list'];
                break;

            case SymbolType::NT_INT_NEXT_LIST . ".1":
                // [ ]
                $header['s.int_list'] = $header['i.int_list'];
                break;

            case SymbolType::NT_STRING_NEXT . ".0":
                $header['s.text_list'] = $symbols[4]['s.text_list'];
                break;

            case SymbolType::NT_STRING_NEXT . ".1":
                $header['s.text_list'] = $header['i.text_list'];
                break;

            case SymbolType::NT_STRING_LIST . ".0":
                $header['s.text_list'] = $symbols[2]['s.text_list'];
                break;

            case SymbolType::NT_STRING . ".0":
            case SymbolType::NT_STRING . ".1":
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . ".0":
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . ".1":
                $header['s.text'] = $symbols[2]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . ".2":
                $header['s.text'] = $header['i.text'];
                break;

            case SymbolType::NT_ESCAPED . ".0":
                $header['s.text'] = '\\';
                break;

            case SymbolType::NT_ESCAPED . ".1":
                $header['s.text'] = '\'';
                break;

            case SymbolType::NT_ESCAPED . ".2":
                $header['s.text'] = '"';
                break;

            case SymbolType::NT_ESCAPED . ".3":
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_BRACKET_FILTER . ".0":
                // [ 0:T_STAR, 1:NT_WS_OPT ]
                $valueList = $this->asNodeValueList($header['i.value_list']);
                $header['s.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...ChildMatcherList::populate(
                            new AnyChildMatcher,
                            ...$valueList->getIndexMap()->getInnerIndice()
                        )
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this->astBuilder->matchAnyChild()
                    );
                break;
            case SymbolType::NT_BRACKET_FILTER . ".1":
                // [ 0:NT_STRING_LIST ]
                $valueList = $this->asNodeValueList($header['i.value_list']);
                $header['s.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...StrictPropertyMatcher::populate(
                            $valueList,
                            ...array_fill_keys(
                                $valueList
                                    ->getIndexMap()
                                    ->getInnerIndice(),
                                $symbols[0]['s.text_list']
                            )
                        )
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->astBuilder
                            ->matchPropertyStrictly(
                                $this->astBuilder->populateNameList(
                                    $header['i.value_list_id'],
                                    ...$symbols[0]['s.text_list']
                                )
                            )
                    );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".2":
                // [ 0:NT_INT, 1:NT_INT_NEXT ]
                $valueList = $this->asNodeValueList($header['i.value_list']);
                $header['s.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...StrictElementMatcher::populate($valueList, ...$symbols[1]['s.int_lists'])
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->astBuilder
                            ->matchElementStrictly($symbols[1]['s.int_lists_id'])
                    );
                break;

            case SymbolType::NT_BRACKET_FILTER . '.3':
                // [ 0:NT_INT_SLICE ]
                $valueList = $symbols[0]['s.value_list'];
                $header['s.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...StrictElementMatcher::populate($valueList, ...$symbols[0]['s.int_lists'])
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $symbols[0]['s.value_list_id'],
                        $this->astBuilder->matchElementStrictly($symbols[0]['s.int_lists_id'])
                    );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4":
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5":
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                $contextValues = $this->asNodeValueList($symbols[3]['i.context_value_list']);
                $evaluationResult = $this
                    ->evaluator
                    ->evaluate(
                        $this->asValueList($symbols[3]['i.value_list']),
                        $this->asValueList($symbols[3]['s.value_list'])
                    );
                $header['s.value_list'] = $this
                    ->fetcher
                    ->filterValues(
                        new ValueListFilter(
                            new EvaluatedValueList(
                                $evaluationResult->getIndexMap()->join($contextValues->getIndexMap()),
                                ...$evaluationResult->getResults()
                            )
                        ),
                        $contextValues
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->filter(
                        $symbols[3]['i.context_value_list_id'],
                        $this
                            ->astBuilder
                            ->evaluate(
                                $symbols[3]['i.value_list_id'],
                                $symbols[3]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0":
                // [ 0:T_OP_NOT, 1:NT_EXPR_ARG_SCALAR ]
                $header['s.value_list'] = $this
                    ->evaluator
                    ->logicalNot(
                        $this
                            ->evaluator
                            ->evaluate(
                                $this->asValueList($header['i.value_list']),
                                $this->asValueList($symbols[1]['s.value_list'])
                            )
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalNot(
                        $this
                            ->astBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $symbols[1]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1":
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0":
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1":
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2":
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3":
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4":
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5":
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $header['s.value_list'] = $symbols[3]['s.value_list'];
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".6":
                // [ 0:T_OP_REGEX, 1:NT_WS_OPT, 2:NT_REGEXP ],
                $header['s.value_list'] = $this
                    ->evaluator
                    ->isRegExp(
                        $this->asValueList($header['i.left_value_list']),
                        $symbols[2]['s.text']
                    );
                $header['s.value_list_id'] = $this
                    ->astBuilder
                    ->calculateIsRegExp(
                        $symbols[2]['s.text'],
                        $header['i.left_value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".8":
                // [ ]
                $header['s.value_list'] = $header['i.left_value_list'];
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0":
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $header['s.value_list'] = $symbols[3]['s.value_list'];
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".1":
                // []
                $header['s.value_list'] = $header['i.left_value_list'];
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0":
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $header['s.value_list'] = $symbols[3]['s.value_list'];
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".1":
                // [ ]
                $header['s.value_list'] = $header['i.left_value_list'];
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR . ".0":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_GROUP . ".0":
                // [ 0:T_LEFT_BRACKET, 1:NT_WS_OPT, 2:NT_EXPR, 3:T_RIGHT_BRACKET]
                $header['s.value_list'] = $symbols[2]['s.value_list'];
                $header['s.value_list_id'] = $symbols[2]['s.value_list_id'];
                break;

            case SymbolType::NT_ARRAY . '.0':
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT, 3:T_RIGHT_SQUARE_BRACKET ]
                $header['s.array_elements'] = $symbols[2]['s.array_elements'];
                $header['s.array_element_ids'] = $symbols[2]['s.array_element_ids'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $header['s.array_elements'] = $symbols[1]['s.array_elements'];
                $header['s.array_element_ids'] = $symbols[1]['s.array_element_ids'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.1':
                // []
                $header['s.array_elements'] = $header['i.array_elements'];
                $header['s.array_element_ids'] = $header['i.array_element_ids'];
                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.0':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT ]
                $header['s.array_elements'] = $symbols[2]['s.array_elements'];
                $header['s.array_element_ids'] = $symbols[2]['s.array_element_ids'];
                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.1':
                // []
                $header['s.array_elements'] = $header['i.array_elements'];
                $header['s.array_element_ids'] = $header['i.array_element_ids'];
                break;

            case SymbolType::NT_REGEXP . '.0':
                // [ 0:T_SLASH, 1:NT_REGEXP_STRING, 2:T_REGEXP_MOD ]
                $header['s.text'] = $symbols[1]['s.text'] . $symbols[2]['s.text'];
                break;

            case SymbolType::NT_REGEXP_STRING . '.0':
                // [ 0:T_UNESCAPED, 1:NT_REGEXP_STRING ]
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_REGEXP_STRING . '.1':
                // [ 0:T_BACKSLASH, 1:NT_REGEXP_ESCAPED, 2:NT_REGEXP_STRING ]
                $header['s.text'] = $symbols[2]['s.text'];
                break;

            case SymbolType::NT_REGEXP_STRING . '.2':
                // [ ]
                $header['s.text'] = $header['i.text'];
                break;

            case SymbolType::NT_REGEXP_ESCAPED . '.0':
                // [ 0:T_SLASH ]
                $header['s.text'] = '\\/';
                break;

            case SymbolType::NT_REGEXP_ESCAPED . '.1':
                // [ 0:T_BACKSLASH ]
                $header['s.text'] = '\\\\';
                break;

            case SymbolType::NT_REGEXP_ESCAPED . '.2':
                // [ 0:T_UNESCAPED ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_NAME . '.0':
                // [ 0:T_NAME ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_NAME . '.1':
                // [ 0:T_NULL ]
                $header['s.text'] = 'null';
                break;

            case SymbolType::NT_NAME . '.2':
                // [ 0:T_TRUE ]
                $header['s.text'] = 'true';
                break;

            case SymbolType::NT_NAME . '.3':
                // [ 0:T_FALSE ]
                $header['s.text'] = 'false';
                break;
        }
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     */
    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $header = $production->getHeaderShortcut();
        $symbols = $production->getSymbolListShortcut();
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}.{$symbolIndex}";
        switch ($hash) {
            case SymbolType::NT_JSON_PATH . ".0.0":
                $symbols[0]['i.is_inline_path'] = false;
                break;

            case SymbolType::NT_PATH . ".0.1":
                // [ 0:T_ROOT_ABSOLUTE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.is_inline_path'] = $header['i.is_inline_path'];
                $symbols[1]['i.value_list'] = NodeValueList::createRoot($this->rootValue);
                $symbols[1]['i.value_list_id'] = $this
                    ->astBuilder
                    ->getInput();
                break;

            case SymbolType::NT_PATH . ".1.1":
                // [ 0:T_ROOT_RELATIVE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.is_inline_path'] = $header['i.is_inline_path'];
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_BRACKET_FILTER . ".2.1":
                // 0:NT_INT, 1:NT_INT_NEXT
                $symbols[1]['i.int'] = $symbols[0]['s.int'];
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.3.0':
                // [ 0:NT_INT_SLICE ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[0]['i.int_start'] = null;
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5.3":
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                $filterContext = $this
                    ->fetcher
                    ->fetchFilterContext($this->asNodeValueList($header['i.value_list']));
                $symbols[3]['i.context_value_list'] = $filterContext;
                $symbols[3]['i.value_list'] = new NodeValueList(
                    $filterContext->getIndexMap()->split(),
                    ...$filterContext->getValues()
                );
                $symbols[3]['i.context_value_list_id'] = $this
                    ->astBuilder
                    ->createFilterContext($header['i.value_list_id']);
                $symbols[3]['i.value_list_id'] = $this
                    ->astBuilder
                    ->split($symbols[3]['i.context_value_list_id']);
                break;

            case SymbolType::NT_EXPR . ".0.0":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR . ".0.1":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.0":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.1":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.2":
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.3":
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $sourceValues = $this->asValueList($header['i.value_list']);
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->logicalOr(
                        $this
                            ->evaluator
                            ->evaluate($sourceValues, $this->asValueList($header['i.left_value_list'])),
                        $this
                            ->evaluator
                            ->evaluate($sourceValues, $this->asValueList($symbols[2]['s.value_list']))
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalOr(
                        $this
                            ->astBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $header['i.left_value_list_id']
                            ),
                        $this
                            ->astBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.0":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.1":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.2":
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.3":
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $sourceValues = $this->asValueList($header['i.value_list']);
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->logicalAnd(
                        $this
                            ->evaluator
                            ->evaluate($sourceValues, $this->asValueList($header['i.left_value_list'])),
                        $this
                            ->evaluator
                            ->evaluate($sourceValues, $this->asValueList($symbols[2]['s.value_list']))
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalAnd(
                        $this
                            ->astBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $header['i.left_value_list_id']
                            ),
                        $this
                            ->astBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0.1":
                // [ 0:T_OP_NOT, 1:NT_EXPR_ARG_SCALAR ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1.0":
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0.0":
                // [ 0:NT_EXPR_GROUP, 1:NT_WS_OPT ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $symbols[0]['i.is_inline_path'] = false;
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2.0":
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".3.0":
                // [ 0:NT_ARRAY, 1:NT_WS_OPT ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.2":
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1.2":
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2.2":
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3.2":
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4.2":
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5.2":
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.3":
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->isEqual(
                        $this->asValueList($header['i.left_value_list']),
                        $this->asValueList($symbols[2]['s.value_list'])
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateIsEqual(
                        $header['i.left_value_list_id'],
                        $symbols[2]['s.value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1.3":
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->logicalNot(
                        $this
                            ->evaluator
                            ->isEqual(
                                $this->asValueList($header['i.left_value_list']),
                                $this->asValueList($symbols[2]['s.value_list'])
                            )
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalNot(
                        $this
                            ->astBuilder
                            ->calculateIsEqual(
                                $header['i.left_value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2.3":
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->isGreater(
                        $this->asValueList($symbols[2]['s.value_list']),
                        $this->asValueList($header['i.left_value_list'])
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateIsGreater(
                        $symbols[2]['s.value_list_id'],
                        $header['i.left_value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3.3":
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->logicalNot(
                        $this
                            ->evaluator
                            ->isGreater(
                                $this->asValueList($header['i.left_value_list']),
                                $this->asValueList($symbols[2]['s.value_list'])
                            )
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalNot(
                        $this
                            ->astBuilder
                            ->calculateIsGreater(
                                $header['i.left_value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4.3":
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->isGreater(
                        $this->asValueList($header['i.left_value_list']),
                        $this->asValueList($symbols[2]['s.value_list'])
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateIsGreater(
                        $header['i.left_value_list_id'],
                        $symbols[2]['s.value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5.3":
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list'] = $header['i.value_list'];
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list'] = $this
                    ->evaluator
                    ->logicalNot(
                        $this
                            ->evaluator
                            ->isGreater(
                                $this->asValueList($symbols[2]['s.value_list']),
                                $this->asValueList($header['i.left_value_list'])
                            )
                    );
                $symbols[3]['i.left_value_list_id'] = $this
                    ->astBuilder
                    ->calculateLogicalNot(
                        $this
                            ->astBuilder
                            ->calculateIsGreater(
                                $symbols[2]['s.value_list_id'],
                                $header['i.left_value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_GROUP . ".0.2":
                // [ 0:T_LEFT_BRACKET, 1:NT_WS_OPT, 2:NT_EXPR, 3:T_RIGHT_BRACKET ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_STRING_LIST . ".0.2":
                // [ 0:NT_STRING, 1:NT_WS_OPT, 2:NT_STRING_NEXT ]
                $symbols[2]['i.text_list'] = [$symbols[0]['s.text']];
                break;

            case SymbolType::NT_STRING_NEXT . ".0.4":
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_STRING, 3:NT_WS_OPT, 4:NT_STRING_NEXT ]
                $symbols[4]['i.text_list'] = array_merge($header['i.text_list'], [$symbols[2]['s.text']]);
                break;

            case SymbolType::NT_INT_NEXT . ".0.1":
                $symbols[1]['i.int_list'] = [$header['i.int']];
                break;

            case SymbolType::NT_INT_NEXT_LIST . ".0.4":
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_INT, 3:NT_WS_OPT, 4:NT_INT_NEXT_LIST ]
                $symbols[4]['i.int_list'] = array_merge($header['i.int_list'], [$symbols[2]['s.int']]);
                break;

            case SymbolType::NT_INT_NEXT . '.1.0':
                // [ 0:NT_INT_SLICE ]
                $symbols[0]['i.value_list'] = $header['i.value_list']; // TODO: probably useless
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id']; // TODO: probably useless
                $symbols[0]['i.int_start'] = $header['i.int'];
                break;

            case SymbolType::NT_FILTER_LIST . ".0.1":
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".1.1":
                // [ 0:T_DOUBLE_DOT, 1:NT_DOUBLE_DOT_FILTER ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2.4":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $symbols[4]['i.value_list'] = $symbols[2]['s.value_list'];
                $symbols[4]['i.value_list_id'] = $symbols[2]['s.value_list_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
                $symbols[1]['i.filter_name'] = $symbols[0]['s.text'];
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1.1":
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $valueList = $this->asNodeValueList($header['i.value_list']);
                $symbols[1]['i.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...ChildMatcherList::populate(
                            new AnyChildMatcher,
                            ...$valueList->getIndexMap()->getInnerIndice()
                        )
                    );
                $symbols[1]['i.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this->astBuilder->matchAnyChild()
                    );
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                // [ 0:NT_FILTER_LIST ]
                $valueList = $this->asNodeValueList($header['i.value_list']);
                $symbols[0]['i.value_list'] = $this
                    ->fetcher
                    ->fetchChildren(
                        $valueList,
                        ...ChildMatcherList::populate(
                            new StrictPropertyMatcher($header['i.filter_name']),
                            ...$valueList->getIndexMap()->getInnerIndice()
                        )
                    );
                $symbols[0]['i.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->astBuilder
                            ->matchPropertyStrictly(
                                $this->astBuilder->populateNameList(
                                    $header['i.value_list_id'],
                                    $header['i.filter_name']
                                )
                            )
                    );
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.0.1':
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list'] = $this
                    ->fetcher
                    ->fetchDeepChildren(
                        new StrictPropertyMatcher($symbols[0]['s.text']),
                        $this->asNodeValueList($header['i.value_list'])
                    );
                $symbols[1]['i.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildrenDeep(
                        $header['i.value_list_id'],
                        $this
                            ->astBuilder
                            ->matchPropertyStrictly(
                                $this->astBuilder->populateNameList(
                                    $header['i.value_list_id'],
                                    $symbols[0]['s.text']
                                )
                            )
                    );
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.1.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list'] = $this
                    ->fetcher
                    ->fetchDeepChildren(
                        new AnyChildMatcher(),
                        $this->asNodeValueList($header['i.value_list'])
                    );
                $symbols[1]['i.value_list_id'] = $this
                    ->astBuilder
                    ->fetchChildrenDeep(
                        $header['i.value_list_id'],
                        $this->astBuilder->matchAnyChild()
                    );
                break;

            case SymbolType::NT_STRING . ".0.1":
            case SymbolType::NT_STRING . ".1.1":
                $symbols[1]['i.text'] = '';
                break;

            case SymbolType::NT_STRING_CONTENT . ".0.1":
                $symbols[1]['i.text'] = $header['i.text'] . $symbols[0]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . ".1.2":
                $symbols[2]['i.text'] = $header['i.text'] . $symbols[1]['s.text'];
                break;

            case SymbolType::NT_ARRAY . '.0.2':
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT, 3:T_RIGHT_SQUARE_BRACKET ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.array_elements'] = [];
                $symbols[2]['i.array_element_ids'] = [];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0.0':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0.1':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.array_elements'] = array_merge(
                    $header['i.array_elements'],
                    [$this->asValueList($symbols[0]['s.value_list'])]
                );
                $symbols[1]['i.array_element_ids'] = array_merge(
                    $header['i.array_element_ids'],
                    [$symbols[0]['s.value_list_id']]
                );

                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.0.2':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.array_elements'] = $header['i.array_elements'];
                $symbols[2]['i.array_element_ids'] = $header['i.array_element_ids'];
                break;

            case SymbolType::NT_REGEXP . '.0.1':
                // [ 0:T_SLASH, 1:NT_REGEXP_STRING, 2:T_REGEXP_MOD ]
                $symbols[1]['i.text'] = '/';
                break;

            case SymbolType::NT_REGEXP_STRING . '.0.1':
                // [ 0:T_UNESCAPED, 1:NT_REGEXP_STRING ]
                $symbols[1]['i.text'] = $header['i.text'] . $symbols[0]['s.text'];
                break;

            case SymbolType::NT_REGEXP_STRING . '.1.2':
                // [ 0:T_BACKSLASH, 1:NT_REGEXP_ESCAPED, 2:NT_REGEXP_STRING ]
                $symbols[2]['i.text'] = $header['i.text'] . $symbols[1]['s.text'];
                break;
        }
    }

    private function asValueList($attribute): ValueListInterface
    {
        if ($attribute instanceof ValueListInterface) {
            return $attribute;
        }

        throw new Exception\InvalidValueListInAttributeException($attribute);
    }

    private function asNodeValueList($attribute): NodeValueListInterface
    {
        if ($attribute instanceof NodeValueListInterface) {
            return $attribute;
        }

        throw new Exception\InvalidValueListInAttributeException($attribute);
    }
}