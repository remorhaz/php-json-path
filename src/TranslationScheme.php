<?php

namespace Remorhaz\JSON\Path;

use function array_pop;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictElementMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    private $fetcher;

    private $rootValue;

    private $outputBuffer = [];

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
        return $this->popOutput();
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
            case SymbolType::NT_JSON_PATH . ".0":
                break;

            case SymbolType::NT_PATH . ".0":
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".0":
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1":
                break;

            case SymbolType::NT_DOT_FILTER . ".0":
                break;

            case SymbolType::NT_DOT_FILTER . ".1":
                break;

            case SymbolType::NT_FILTER_LIST . ".0":
                break;

            case SymbolType::NT_FILTER_LIST . ".1":
                break;

            case SymbolType::NT_FILTER_LIST . ".2":
                break;

            case SymbolType::NT_FILTER_LIST . ".3":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2":
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
                // T_STAR, NT_WS_OPT
                $this->pushOutput(
                    ...$this->fetcher->fetchChildren(
                        new AnyChildMatcher,
                        ...$this->popOutput()
                    )
                );
                break;
            case SymbolType::NT_BRACKET_FILTER . ".1":
                // NT_STRING_LIST
                $this->pushOutput(
                    ...$this->fetcher->fetchChildren(
                        new StrictPropertyMatcher(...$symbols[0]['s.text_list']),
                        ...$this->popOutput()
                    )
                );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".2":
                // [ 0:NT_INT, 1:NT_INT_NEXT ]
                $this->pushOutput(
                    ...$this->fetcher->fetchChildren(
                        new StrictElementMatcher(...$symbols[1]['s.int_list']),
                        ...$this->popOutput()
                    )
                );
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4":
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1":
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".8":
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".1":
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0":
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".1":
                break;

            case SymbolType::NT_EXPR . ".0":
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
                $rootName = $symbols[0]['s.text'];
                $isInlinePath = $header['i.is_inline_path'];
                if ('$' == $rootName) {
                    $this->pushOutput($this->rootValue);
                }
                $symbols[1]['i.is_inline_path'] = $isInlinePath;
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
                break;

            case SymbolType::NT_EXPR . ".0.0":
                break;

            case SymbolType::NT_EXPR . ".0.1":
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.0":
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.1":
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.2":
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.0":
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.1":
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.2":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0.1":
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1.0":
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".2.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".3.0":
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.2":
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
                break;

            case SymbolType::NT_FILTER_LIST . ".1.2":
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                break;

            case SymbolType::NT_FILTER_LIST . ".2.4":
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                $symbols[1]['i.filter_name'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1.1":
                $this->pushOutput(
                    ...$this->fetcher->fetchChildren(
                        new AnyChildMatcher,
                        ...$this->popOutput()
                    )
                );
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                $this->pushOutput(
                    ...$this->fetcher->fetchChildren(
                        new StrictPropertyMatcher($header['i.filter_name']),
                        ...$this->popOutput()
                    )
                );
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                $symbols[0]['i.is_inline_path'] = true;
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

    private function pushOutput(ValueInterface ...$values): void
    {
        $this->outputBuffer[] = $values;
    }

    /**
     * @return ValueInterface[]
     */
    private function popOutput(): array
    {
        $output = array_pop($this->outputBuffer);
        if (isset($output)) {
            return $output;
        }

        throw new Exception\EmptyOutputBufferException();
    }
}
