<?php

/** @noinspection PhpDuplicateSwitchCaseBodyInspection */

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Query\AstBuilderInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

use function array_merge;

class TranslationScheme implements TranslationSchemeInterface
{
    public function __construct(
        private AstBuilderInterface $queryAstBuilder,
    ) {
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
                $s['s.text'] = $t['text'];
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
            case SymbolType::NT_ROOT . '.0':
                // [ 0:NT_JSON_PATH, 1:T_EOI ]
                $this
                    ->queryAstBuilder
                    ->setOutput(
                        $symbols[0]['s.value_list_id'],
                        $symbols[0]['s.is_definite'],
                        $symbols[0]['s.is_addressable']
                    );
                break;

            case SymbolType::NT_JSON_PATH . '.0':
                // [ 0:NT_PATH ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[0]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[0]['s.is_addressable'];
                break;

            case SymbolType::NT_PATH . '.0':
                // [ 0:T_ROOT_ABSOLUTE, 1:NT_FILTER_LIST ]
            case SymbolType::NT_PATH . '.1':
                // [ 0:T_ROOT_RELATIVE, 1:NT_FILTER_LIST ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . '.0':
                // [ 0:T_LEFT_BRACKET, 1:T_RIGHT_BRACKET ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->aggregate(
                        $header['i.filter_name'],
                        $header['i.value_list_id']
                    );
                $header['s.is_definite'] = $header['i.is_definite'];
                $header['s.is_addressable'] = false;
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . '.1':
                // [ 0:NT_FILTER_LIST ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[0]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[0]['s.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER . '.0':
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
            case SymbolType::NT_DOT_FILTER . '.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOT_FILTER . '.2':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOT_FILTER . '.3':
                // [ 0:NT_STRING, 1:NT_FILTER_LIST ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.0':
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOUBLE_DOT_FILTER . '.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOUBLE_DOT_FILTER . '.2':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOUBLE_DOT_FILTER . '.3':
                // [ 0:NT_STRING, 1:NT_FILTER_LIST ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.0':
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.1':
                // [ 0:T_DOUBLE_DOT, 1:NT_DOUBLE_DOT_FILTER ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = false;
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.2':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                $header['s.is_addressable'] = $symbols[1]['s.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.3':
                // [ ]
                $header['s.value_list_id'] = $header['i.value_list_id'];
                $header['s.is_definite'] = $header['i.is_definite'];
                $header['s.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_PREDICATE . '.0':
                // [
                //   0:T_LEFT_SQUARE_BRACKET,
                //   1:NT_WS_OPT,
                //   2:NT_BRACKET_FILTER,
                //   3:T_RIGHT_SQUARE_BRACKET,
                // ]
                $header['s.value_list_id'] = $symbols[2]['s.value_list_id'];
                $header['s.is_definite'] = $symbols[2]['s.is_definite'];
                $header['s.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.0':
                // [ 0:NT_EXPR_GROUP, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.1':
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.2':
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createScalar($header['i.value_list_id'], $symbols[0]['s.int']);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.3':
                // [ 0:NT_ARRAY, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createLiteralArray($header['i.value_list_id'], $symbols[0]['s.array_id']);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.4':
                // [ 0:T_NULL, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createScalar($header['i.value_list_id'], null);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.5':
                // [ 0:T_TRUE, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createScalar($header['i.value_list_id'], true);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.6':
                // [ 0:T_FALSE, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createScalar($header['i.value_list_id'], false);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.7':
                // [ 0:NT_STRING, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->createScalar($header['i.value_list_id'], $symbols[0]['s.text']);
                break;

            case SymbolType::NT_INT . '.0':
                // [ 0:T_HYPHEN, 1:T_INT ]
                $header['s.int'] = -$symbols[1]['s.int'];
                break;

            case SymbolType::NT_INT . '.1':
                // [ 0:T_INT ]
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_INT_NEXT . '.0':
                // [ 0:NT_WS_OPT, 1:NT_INT_NEXT_LIST ]
                $header['s.matcher_id'] = $this
                    ->queryAstBuilder
                    ->matchElementStrictly(...$symbols[1]['s.int_list']);
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                break;

            case SymbolType::NT_INT_NEXT . '.1':
                // [ 0:NT_INT_SLICE ]
                $header['s.matcher_id'] = $symbols[0]['s.matcher_id'];
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_INT_SLICE_OPT . '.0':
                // [ 0:NT_INT_SLICE ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                $header['s.matcher_id'] = $symbols[0]['s.matcher_id'];
                break;

            case SymbolType::NT_INT_SLICE_OPT . '.1':
                // [ ]
                $header['s.value_list_id'] = $header['i.value_list_id'];
                $header['s.matcher_id'] = $this
                    ->queryAstBuilder
                    ->matchElementSlice(
                        $header['i.int_start'],
                        null,
                        null,
                    );
                break;

            case SymbolType::NT_INT_SLICE . '.0':
                // [ 0:T_COLON, 1:NT_INT_OPT, 2:NT_INT_SLICE_STEP, 3:NT_WS_OPT ]
                $header['s.value_list_id'] = $header['i.value_list_id'];
                $header['s.matcher_id'] = $this
                    ->queryAstBuilder
                    ->matchElementSlice(
                        $header['i.int_start'],
                        $symbols[1]['s.int'],
                        $symbols[2]['s.int'],
                    );
                break;

            case SymbolType::NT_INT_OPT . '.0':
                // [ 0:NT_INT ]
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_INT_OPT . '.1':
                // []
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

            case SymbolType::NT_INT_NEXT_LIST . '.0':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_INT, 3:NT_WS_OPT, 4:NT_INT_NEXT_LIST ]
                $header['s.int_list'] = $symbols[4]['s.int_list'];
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_INT_NEXT_LIST . '.1':
                // [ ]
                $header['s.int_list'] = $header['i.int_list'];
                $header['s.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_STRING_NEXT . '.0':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_PROPERTY, 3:NT_WS_OPT, 4:NT_STRING_NEXT ]
                $header['s.text_list'] = $symbols[4]['s.text_list'];
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_STRING_NEXT . '.1':
                // [ ]
                $header['s.text_list'] = $header['i.text_list'];
                $header['s.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_STRING_LIST . '.0':
                // [ 0:NT_PROPERTY, 1:NT_WS_OPT, 2:NT_STRING_NEXT ]
                $header['s.text_list'] = $symbols[2]['s.text_list'];
                $header['s.is_definite'] = $symbols[2]['s.is_definite'];
                break;

            case SymbolType::NT_PROPERTY . '.0':
                // [ 0:NT_STRING ]
            case SymbolType::NT_PROPERTY . '.1':
                // [ 0:NT_NAME ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_STRING . '.0':
                // [ 0:T_SINGLE_QUOTE, 1:NT_STRING_CONTENT, 2:T_SINGLE_QUOTE ]
            case SymbolType::NT_STRING . '.1':
                // [ 0:T_DOUBLE_QUOTE, 1:NT_STRING_CONTENT, 2:T_DOUBLE_QUOTE ]
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . '.0':
                // [ 0:T_UNESCAPED, 1:NT_STRING_CONTENT ]
                $header['s.text'] = $symbols[1]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . '.1':
                // [ 0:T_BACKSLASH, 1:NT_ESCAPED, 2:NT_STRING_CONTENT ]
                $header['s.text'] = $symbols[2]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . '.2':
                // [ ]
                $header['s.text'] = $header['i.text'];
                break;

            case SymbolType::NT_ESCAPED . '.0':
                // [ 0:T_BACKSLASH ]
                $header['s.text'] = '\\';
                break;

            case SymbolType::NT_ESCAPED . '.1':
                // [ 0:T_SINGLE_QUOTE ]
                $header['s.text'] = '\'';
                break;

            case SymbolType::NT_ESCAPED . '.2':
                // [ 0:T_DOUBLE_QUOTE ]
                $header['s.text'] = '"';
                break;

            case SymbolType::NT_ESCAPED . '.3':
                // [ 0:T_UNESCAPED ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.0':
                // [ 0:T_STAR, 1:NT_WS_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this->queryAstBuilder->matchAnyChild()
                    );
                $header['s.is_definite'] = false;
                break;
            case SymbolType::NT_BRACKET_FILTER . '.1':
                // [ 0:NT_STRING_LIST ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->matchPropertyStrictly(...$symbols[0]['s.text_list'])
                    );
                $header['s.is_definite'] = $symbols[0]['s.is_definite'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.2':
                // [ 0:T_INT, 1:NT_INT_NEXT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren($header['i.value_list_id'], $symbols[1]['s.matcher_id']);
                $header['s.is_definite'] = $symbols[1]['s.is_definite'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.3':
                // [ 0:T_HYPHEN, 1:T_INT, 2:NT_INT_SLICE_OPT ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren($symbols[2]['s.value_list_id'], $symbols[2]['s.matcher_id']);
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_BRACKET_FILTER . '.4':
                // [ 0:NT_INT_SLICE ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren($symbols[0]['s.value_list_id'], $symbols[0]['s.matcher_id']);
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_BRACKET_FILTER . '.5':
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->filter(
                        $symbols[3]['i.context_value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->joinFilterResults(
                                $this
                                    ->queryAstBuilder
                                    ->evaluate($symbols[3]['i.value_list_id'], $symbols[3]['s.value_list_id']),
                                $symbols[3]['i.context_value_list_id'],
                            )
                    );
                $header['s.is_definite'] = false;
                break;

            case SymbolType::NT_EXPR_ARG_COMP . '.0':
                // [ 0:T_OP_NOT, 1:NT_EXPR_ARG_SCALAR ]
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalNot(
                        $this
                            ->queryAstBuilder
                            ->evaluate($header['i.value_list_id'], $symbols[1]['s.value_list_id'])
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . '.1':
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $header['s.value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.0':
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.1':
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.2':
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.3':
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.4':
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.5':
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.6':
                // [ 0:T_OP_REGEX, 1:NT_WS_OPT, 2:NT_REGEXP ],
                $header['s.value_list_id'] = $this
                    ->queryAstBuilder
                    ->calculateIsRegExp($symbols[2]['s.text'], $header['i.left_value_list_id']);
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.7':
                // [ ]
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . '.0':
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . '.0':
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . '.1':
                // []
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . '.0':
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . '.0':
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $header['s.value_list_id'] = $symbols[3]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . '.1':
                // [ ]
                $header['s.value_list_id'] = $header['i.left_value_list_id'];
                break;

            case SymbolType::NT_EXPR . '.0':
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $header['s.value_list_id'] = $symbols[1]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_GROUP . '.0':
                // [ 0:T_LEFT_BRACKET, 1:NT_WS_OPT, 2:NT_EXPR, 3:T_RIGHT_BRACKET]
                $header['s.value_list_id'] = $symbols[2]['s.value_list_id'];
                break;

            case SymbolType::NT_ARRAY . '.0':
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT, 3:T_RIGHT_SQUARE_BRACKET ]
                $header['s.array_id'] = $symbols[2]['s.array_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $header['s.array_id'] = $symbols[1]['s.array_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.1':
                // []
                $header['s.array_id'] = $header['i.array_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.0':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT ]
                $header['s.array_id'] = $symbols[2]['s.array_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.1':
                // []
                $header['s.array_id'] = $header['i.array_id'];
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

            case SymbolType::NT_DOT_NAME_TAIL . '.0':
                // [ 0:NT_NAME ]
                $header['s.text'] = $header['i.text'] . $symbols[0]['s.text'];
                break;

            case SymbolType::NT_DOT_NAME_TAIL . '.1':
                // [ ]
                $header['s.text'] = $header['i.text'];
                break;

            case SymbolType::NT_DOT_NAME . '.0':
                // [ 0:NT_NAME ]
                $header['s.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_DOT_NAME . '.1':
                // [ 0:NT_INT, 1:NT_DOT_NAME_TAIL ]
                $header['s.text'] = $symbols[1]['s.text'];
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
            case SymbolType::NT_JSON_PATH . '.0.0':
                // [ 0:NT_PATH ]
                $symbols[0]['i.is_definite'] = true;
                break;

            case SymbolType::NT_PATH . '.0.1':
                // [ 0:T_ROOT_ABSOLUTE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->getInput();
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                $symbols[1]['i.is_addressable'] = true;
                break;

            case SymbolType::NT_PATH . '.1.1':
                // [ 0:T_ROOT_RELATIVE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                $symbols[1]['i.is_addressable'] = true;
                break;

            case SymbolType::NT_BRACKET_FILTER . '.1.0':
                // [ 0:NT_STRING_LIST ]
                $symbols[0]['i.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.2.1':
                // [ 0:NT_INT, 1:NT_INT_NEXT ]
                $symbols[1]['i.int'] = $symbols[0]['s.int'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.3.2':
                // [ 0:T_HYPHEN, 1:T_INT, 2:NT_INT_SLICE_OPT ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.int_start'] = -$symbols[1]['s.int'];
                break;

            case SymbolType::NT_BRACKET_FILTER . '.4.0':
                // [ 0:NT_INT_SLICE ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[0]['i.int_start'] = null;
                break;

            case SymbolType::NT_BRACKET_FILTER . '.5.3':
                // [ 0:T_QUESTION, 1:T_LEFT_BRACKET, 2:NT_WS_OPT, 3:NT_EXPR, 4:T_RIGHT_BRACKET ]
                $symbols[3]['i.context_value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchFilterContext($header['i.value_list_id']);
                $symbols[3]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->splitFilterContext($symbols[3]['i.context_value_list_id']);
                break;

            case SymbolType::NT_DOT_NAME . '.1.1':
                // [ 0:T_INT, 1:NT_DOT_NAME_TAIL ]
                $symbols[1]['i.text'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_EXPR . '.0.0':
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR . '.0.1':
                // [ 0:NT_EXPR_ARG_OR, 1:NT_EXPR_ARG_OR_TAIL ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . '.0.0':
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . '.0.1':
                // [ 0:NT_EXPR_ARG_AND, 1:NT_EXPR_ARG_AND_TAIL ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . '.0.2':
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . '.0.3':
                // [ 0:T_OP_OR, 1:NT_WS_OPT, 2:NT_EXPR_ARG_OR, 3:NT_EXPR_ARG_OR_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalOr(
                        $this
                            ->queryAstBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $header['i.left_value_list_id']
                            ),
                        $this
                            ->queryAstBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_AND . '.0.0':
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . '.0.1':
                // [ 0:NT_EXPR_ARG_COMP, 1:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.left_value_list_id'] = $symbols[0]['s.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . '.0.2':
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . '.0.3':
                // [ 0:T_OP_AND, 1:NT_WS_OPT, 2:NT_EXPR_ARG_AND, 3:NT_EXPR_ARG_AND_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalAnd(
                        $this
                            ->queryAstBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $header['i.left_value_list_id']
                            ),
                        $this
                            ->queryAstBuilder
                            ->evaluate(
                                $header['i.value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP . '.0.1':
                // [ 0:T_OP_NOT, 1:NT_EXPR_ARG_SCALAR ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP . '.1.0':
                // [ 0:NT_EXPR_ARG_SCALAR ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.0.0':
                // [ 0:NT_EXPR_GROUP, 1:NT_WS_OPT ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.1.0':
                // [ 0:NT_PATH, 1:NT_WS_OPT ]
                $symbols[0]['i.is_definite'] = false;
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.2.0':
                // [ 0:NT_INT, 1:NT_WS_OPT ]
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . '.3.0':
                // [ 0:NT_ARRAY, 1:NT_WS_OPT ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.0.2':
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.1.2':
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.2.2':
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.3.2':
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.4.2':
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.5.2':
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.0.3':
                // [ 0:T_OP_EQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->calculateIsEqual(
                        $header['i.left_value_list_id'],
                        $symbols[2]['s.value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.1.3':
                // [ 0:T_OP_NEQ, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalNot(
                        $this
                            ->queryAstBuilder
                            ->calculateIsEqual(
                                $header['i.left_value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.2.3':
                // [ 0:T_OP_L, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->calculateIsGreater(
                        $symbols[2]['s.value_list_id'],
                        $header['i.left_value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.3.3':
                // [ 0:T_OP_LE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalNot(
                        $this
                            ->queryAstBuilder
                            ->calculateIsGreater(
                                $header['i.left_value_list_id'],
                                $symbols[2]['s.value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.4.3':
                // [ 0:T_OP_G, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->calculateIsGreater(
                        $header['i.left_value_list_id'],
                        $symbols[2]['s.value_list_id']
                    );
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . '.5.3':
                // [ 0:T_OP_GE, 1:NT_WS_OPT, 2:NT_EXPR_ARG_COMP, 3:NT_EXPR_ARG_COMP_TAIL ]
                $symbols[3]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[3]['i.left_value_list_id'] = $this
                    ->queryAstBuilder
                    ->evaluateLogicalNot(
                        $this
                            ->queryAstBuilder
                            ->calculateIsGreater(
                                $symbols[2]['s.value_list_id'],
                                $header['i.left_value_list_id']
                            )
                    );
                break;

            case SymbolType::NT_EXPR_GROUP . '.0.2':
                // [ 0:T_LEFT_BRACKET, 1:NT_WS_OPT, 2:NT_EXPR, 3:T_RIGHT_BRACKET ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_STRING_LIST . '.0.2':
                // [ 0:NT_PROPERTY, 1:NT_WS_OPT, 2:NT_STRING_NEXT ]
                $symbols[2]['i.text_list'] = [$symbols[0]['s.text']];
                $symbols[2]['i.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_STRING_NEXT . '.0.4':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_PROPERTY, 3:NT_WS_OPT, 4:NT_STRING_NEXT ]
                $symbols[4]['i.text_list'] = array_merge($header['i.text_list'], [$symbols[2]['s.text']]);
                $symbols[4]['i.is_definite'] = false;
                break;

            case SymbolType::NT_INT_NEXT . '.0.1':
                // [ 0:NT_WS_OPT, 1:NT_INT_NEXT_LIST ]
                $symbols[1]['i.int_list'] = [$header['i.int']];
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_INT_NEXT_LIST . '.0.4':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_INT, 3:NT_WS_OPT, 4:NT_INT_NEXT_LIST ]
                $symbols[4]['i.int_list'] = array_merge($header['i.int_list'], [$symbols[2]['s.int']]);
                $symbols[4]['i.is_definite'] = false;
                break;

            case SymbolType::NT_INT_NEXT . '.1.0':
                // [ 0:NT_INT_SLICE ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id']; // TODO: probably useless
                $symbols[0]['i.int_start'] = $header['i.int'];
                break;

            case SymbolType::NT_INT_SLICE_OPT . '.0.0':
                // [ 0:NT_INT_SLICE ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[0]['i.int_start'] = $header['i.int_start'];
                break;

            case SymbolType::NT_FILTER_LIST . '.0.1':
                // [ 0:T_DOT, 1:NT_DOT_FILTER ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.1.1':
                // [ 0:T_DOUBLE_DOT, 1:NT_DOUBLE_DOT_FILTER ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.2.0':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[0]['i.is_definite'] = $header['i.is_definite'];
                $symbols[0]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_FILTER_LIST . '.2.1':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $symbols[0]['s.value_list_id'];
                $symbols[1]['i.is_definite'] = $symbols[0]['s.is_definite'];
                $symbols[1]['i.is_addressable'] = $symbols[0]['s.is_addressable'];
                break;

            case SymbolType::NT_PREDICATE . '.0.2':
                // [
                //   0:T_LEFT_SQUARE_BRACKET,
                //   1:NT_WS_OPT,
                //   2:NT_BRACKET_FILTER,
                //   3:T_RIGHT_SQUARE_BRACKET,
                // ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.is_definite'] = $header['i.is_definite'];
                break;

            case SymbolType::NT_DOT_FILTER . '.0.1':
                // [ 0:T_NAME, 1:NT_DOT_FILTER_NEXT ]
                $symbols[1]['i.filter_name'] = $symbols[0]['s.text'];
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER . '.3.1':
                // [ 0:NT_STRING, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->matchPropertyStrictly($symbols[0]['s.text'])
                    );
                $symbols[1]['i.is_definite'] = $header['i.is_definite'];
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER . '.1.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this->queryAstBuilder->matchAnyChild()
                    );
                $symbols[1]['i.is_definite'] = false;
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER . '.2.0':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[0]['i.is_definite'] = $header['i.is_definite'];
                $symbols[0]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER . '.2.1':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $symbols[0]['s.value_list_id'];
                $symbols[1]['i.is_definite'] = $symbols[0]['s.is_definite'];
                $symbols[1]['i.is_addressable'] = $symbols[0]['s.is_addressable'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . '.1.0':
                // [ 0:NT_FILTER_LIST ]
                $symbols[0]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildren(
                        $header['i.value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->matchPropertyStrictly($header['i.filter_name'])
                    );
                $symbols[0]['i.is_definite'] = $header['i.is_definite'];
                $symbols[0]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.0.1':
                // [ 0:T_NAME, 1:NT_FILTER_LIST ]
            case SymbolType::NT_DOUBLE_DOT_FILTER . '.3.1':
                // [ 0:NT_STRING, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildrenDeep(
                        $header['i.value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->matchPropertyStrictly($symbols[0]['s.text'])
                    );
                $symbols[1]['i.is_definite'] = false;
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.1.1':
                // [ 0:T_STAR, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->fetchChildrenDeep(
                        $header['i.value_list_id'],
                        $this->queryAstBuilder->matchAnyChild()
                    );
                $symbols[1]['i.is_definite'] = false;
                $symbols[1]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.2.0':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[0]['i.value_list_id'] = $this
                    ->queryAstBuilder
                    ->merge(
                        $header['i.value_list_id'],
                        $this
                            ->queryAstBuilder
                            ->fetchChildrenDeep(
                                $header['i.value_list_id'],
                                $this->queryAstBuilder->matchAnyChild()
                            ),
                    );
                $symbols[0]['i.is_definite'] = false;
                $symbols[0]['i.is_addressable'] = $header['i.is_addressable'];
                break;

            case SymbolType::NT_DOUBLE_DOT_FILTER . '.2.1':
                // [ 0:NT_PREDICATE, 1:NT_FILTER_LIST ]
                $symbols[1]['i.value_list_id'] = $symbols[0]['s.value_list_id'];
                $symbols[1]['i.is_definite'] = $symbols[0]['s.is_definite'];
                $symbols[1]['i.is_addressable'] = $symbols[0]['s.is_addressable'];
                break;

            case SymbolType::NT_STRING . '.0.1':
                // [ 0:T_SINGLE_QUOTE, 1:NT_STRING_CONTENT, 2:T_SINGLE_QUOTE ]
            case SymbolType::NT_STRING . '.1.1':
                // [ 0:T_DOUBLE_QUOTE, 1:NT_STRING_CONTENT, 2:T_DOUBLE_QUOTE ]
                $symbols[1]['i.text'] = '';
                break;

            case SymbolType::NT_STRING_CONTENT . '.0.1':
                // [ 0:T_UNESCAPED, 1:NT_STRING_CONTENT ]
                $symbols[1]['i.text'] = $header['i.text'] . $symbols[0]['s.text'];
                break;

            case SymbolType::NT_STRING_CONTENT . '.1.2':
                // [ 0:T_BACKSLASH, 1:NT_ESCAPED, 2:NT_STRING_CONTENT ]
                $symbols[2]['i.text'] = $header['i.text'] . $symbols[1]['s.text'];
                break;

            case SymbolType::NT_ARRAY . '.0.2':
                // [ 0:T_LEFT_SQUARE_BRACKET, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT, 3:T_RIGHT_SQUARE_BRACKET ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.array_id'] = $this
                    ->queryAstBuilder
                    ->createArray();
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0.0':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $symbols[0]['i.value_list_id'] = $header['i.value_list_id'];
                break;

            case SymbolType::NT_ARRAY_CONTENT . '.0.1':
                // [ 0:NT_EXPR, 1:NT_ARRAY_CONTENT_TAIL ]
                $symbols[1]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[1]['i.array_id'] = $this
                    ->queryAstBuilder
                    ->appendToArray($header['i.array_id'], $symbols[0]['s.value_list_id']);

                break;

            case SymbolType::NT_ARRAY_CONTENT_TAIL . '.0.2':
                // [ 0:T_COMMA, 1:NT_WS_OPT, 2:NT_ARRAY_CONTENT ]
                $symbols[2]['i.value_list_id'] = $header['i.value_list_id'];
                $symbols[2]['i.array_id'] = $header['i.array_id'];
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
}
