<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictElementMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilter;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueList;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    private $fetcher;

    private $rootValue;

    private $output;

    public function __construct(ValueInterface $rootValue, Fetcher $fetcher)
    {
        $this->rootValue = $rootValue;
        $this->fetcher = $fetcher;
    }

    /**
     * @return ValueInterface[]
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
                /** @var ValueListInterface $valueList */
                $valueList = $symbols[0]['s.value_list'];
                $this->output = $valueList->getValues();
                break;

            case SymbolType::NT_JSON_PATH . ".0":
                // [ 0:NT_PATH ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_PATH . ".0":
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".0":
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1":
                // [ 0:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0":
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1":
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_FILTER_LIST . ".0":
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_FILTER_LIST . ".1":
                break;

            case SymbolType::NT_FILTER_LIST . ".2":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $header['s.value_list'] = $symbols[4]['s.value_list'];
                break;

            case SymbolType::NT_FILTER_LIST . ".3":
                // [ ]
                $header['s.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1":
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2":
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                $header['s.value_list'] = $this
                    ->fetcher
                    ->createScalarList($header['i.value_list'], $symbols[0]['s.int']);
                break;

            case SymbolType::NT_INT . ".0":
                $header['s.int'] = -$symbols[1]['s.int'];
                break;

            case SymbolType::NT_INT . ".1":
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_INT_NEXT . ".0":
                // [ 0:NT_WS_OPT, 1:NT_INT_NEXT_LIST ]
                $header['s.int_list'] = $symbols[1]['s.int_list'];
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
                $header['s.value_list'] = $this->fetcher->fetchChildren(
                    new AnyChildMatcher,
                    $header['i.value_list']
                );
                break;
            case SymbolType::NT_BRACKET_FILTER . ".1":
                // [ 0:NT_STRING_LIST ]
                $header['s.value_list'] = $this->fetcher->fetchChildren(
                    new StrictPropertyMatcher(...$symbols[0]['s.text_list']),
                    $header['i.value_list']
                );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".2":
                // [ 0:NT_INT, 1:NT_INT_NEXT ]
                $header['s.value_list'] = $this->fetcher->fetchChildren(
                    new StrictElementMatcher(...$symbols[1]['s.int_list']),
                    $header['i.value_list']
                );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4":
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5":
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                $header['s.value_list'] = $this->fetcher->filterValues(
                    new ValueListFilter($symbols[3]['s.value_list']),
                    $symbols[3]['i.value_list']
                );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1":
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $header['s.value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0":
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP]
                $header['s.value_list'] = $this
                    ->fetcher
                    ->isEqual($header['i.left_value_list'], $symbols[2]['s.value_list']);
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".8":
                // [ ]
                $header['s.value_list'] = $header['i.left_value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0":
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND]
                $header['s.value_list'] = $this
                    ->fetcher
                    ->logicalAnd($header['i.left_value_list'], $symbols[2]['s.value_list']);
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".1":
                // []
                $header['s.value_list'] = $header['i.left_value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0":
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR]
                $header['s.value_list'] = $this
                    ->fetcher
                    ->logicalOr($header['i.left_value_list'], $symbols[2]['s.value_list']);
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".1":
                // [ ]
                $header['s.value_list'] = $header['i.left_value_list'];
                break;

            case SymbolType::NT_EXPR . ".0":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $header['s.value_list'] = $symbols[1]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_GROUP . ".0":
                break;
        }
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     * @throws Exception
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
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
                $symbols[1]['i.is_inline_path'] = $header['i.is_inline_path'];
                $rootName = $symbols[0]['s.text'];
                if ('$' == $rootName) {
                    $symbols[1]['i.value_list'] = ValueList::create($this->rootValue);
                } elseif ('@' == $rootName) {
                    $symbols[1]['i.value_list'] = $header['i.value_list'];
                } elseif ('null' === $rootName) {
                    $symbols[1]['i.value_list'] = $this
                        ->fetcher
                        ->createScalarList($header['i.value_list'], null);
                }
                break;

            case SymbolType::NT_BRACKET_FILTER . ".1.0":
                break;

            case SymbolType::NT_BRACKET_FILTER . ".2.1":
                // 0:NT_INT, 1:NT_INT_NEXT
                $symbols[1]['i.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4.2":
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5.3":
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                /** @var ValueListInterface $valueList */
                $valueList = $header['i.value_list'];
                $children = $this
                    ->fetcher
                    ->fetchFilterContext($valueList);
                $symbols[3]['i.value_list'] = ValueList::create(...$children->getValues());
                break;

            case SymbolType::NT_EXPR . ".0.0":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR . ".0.1":
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.0":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.1":
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.2":
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.0":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.1":
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                $symbols[1]['i.left_value_list'] = $symbols[0]['s.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.2":
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0.1":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1.0":
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0.0":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $symbols[0]['i.is_inline_path'] = false;
                $symbols[0]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2.0":
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".3.0":
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.2":
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".6.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".7.2":
                break;

            case SymbolType::NT_EXPR_GROUP . ".0.2":
                break;

            case SymbolType::NT_STRING_LIST . ".0.2":
                // [ 0:NT_STRING, 1:NT_WS_OPT, 2:NT_STRING_NEXT ]
                $symbols[2]['i.text_list'] = [$symbols[0]['s.text']];
                break;

            case SymbolType::NT_STRING_NEXT . ".0.4":
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_STRING, 3:NT_WS_OPT, 4:NT_STRING_NEXT ]
                $textList = $header['i.text_list'];
                $textList[] = $symbols[2]['s.text'];
                $symbols[4]['i.text_list'] = $textList;
                break;

            case SymbolType::NT_INT_NEXT . ".0.1":
                // [ 0:NT_WS_OPT, 1:NT_INT_NEXT_LIST ]
                $symbols[1]['i.int_list'] = [$header['i.int']];
                break;

            case SymbolType::NT_INT_NEXT_LIST . ".0.4":
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_INT, 3:NT_WS_OPT, 4:NT_INT_NEXT_LIST ]
                $intList = $header['i.int_list'];
                $intList[] = $symbols[2]['s.int'];
                $symbols[4]['i.int_list'] = $intList;
                break;

            case SymbolType::NT_FILTER_LIST . ".0.1":
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_FILTER_LIST . ".1.2":
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $symbols[2]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2.4":
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_BRACKET_FILTER, 3:T_RIGHT_SQUARE_BRACKET, 4:NT_FILTER_LIST ]
                $symbols[4]['i.value_list'] = $symbols[2]['s.value_list'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
                $symbols[1]['i.filter_name'] = $symbols[0]['s.text'];
                $symbols[1]['i.value_list'] = $header['i.value_list'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1.1":
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list'] = $this->fetcher->fetchChildren(
                    new AnyChildMatcher,
                    $header['i.value_list']
                );
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                // [ 0:NT_FILTER_LIST ]
                $symbols[0]['i.value_list'] = $this->fetcher->fetchChildren(
                    new StrictPropertyMatcher($header['i.filter_name']),
                    $header['i.value_list']
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
        }
    }
}
