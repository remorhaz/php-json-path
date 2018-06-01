<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    /**
     * @param Symbol $symbol
     * @param Token $token
     * @throws \Remorhaz\UniLex\Exception
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        switch ($symbol->getSymbolId()) {
            case SymbolType::T_NAME:
            case SymbolType::T_UNESCAPED:
                $text = $token->getAttribute('text');
                $symbol->setAttribute('s.text', $text);
                break;

            case SymbolType::T_INT:
                $text = $token->getAttribute('text');
                $symbol->setAttribute('s.int', intval($text));
                break;
        }
    }

    /**
     * @param Production $production
     * @throws \Remorhaz\UniLex\Exception
     */
    public function applyProductionActions(Production $production): void
    {
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}";
        switch ($hash) {
            case SymbolType::NT_JSON_PATH . ".0":
                var_dump("RETURN BUFFER");
                break;

            case SymbolType::NT_INT . ".0":
                $int = $production
                    ->getSymbol(1)
                    ->getAttribute('s.int');
                $production
                    ->getHeader()
                    ->setAttribute('s.int', -$int);
                break;

            case SymbolType::NT_INT . ".1":
                $int = $production
                    ->getSymbol(0)
                    ->getAttribute('s.int');
                $production
                    ->getHeader()
                    ->setAttribute('s.int', $int);
                break;

            case SymbolType::NT_STRING . ".0":
            case SymbolType::NT_STRING . ".1":
                $text = $production
                    ->getSymbol(1)
                    ->getAttribute('s.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_STRING_CONTENT . ".0":
                $text = $production
                    ->getSymbol(1)
                    ->getAttribute('i.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_STRING_CONTENT . ".1":
                $text = $production
                    ->getSymbol(2)
                    ->getAttribute('i.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_STRING_CONTENT . ".2":
                $text = $production
                    ->getHeader()
                    ->getAttribute('i.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_ESCAPED . ".0":
                $production
                    ->getHeader()
                    ->setAttribute('s.text', '\\');
                break;

            case SymbolType::NT_ESCAPED . ".1":
                $production
                    ->getHeader()
                    ->setAttribute('s.text', '\'');
                break;

            case SymbolType::NT_ESCAPED . ".2":
                $production
                    ->getHeader()
                    ->setAttribute('s.text', '"');
                break;

            case SymbolType::NT_ESCAPED . ".3":
                $text = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_BRACKET_FILTER . ".1":
                var_dump("SKIP IF NOT: NAME == '?'");
                break;
        }
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     * @throws Exception
     * @throws \Remorhaz\UniLex\Exception
     */
    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $hash = "{$production->getHeader()->getSymbolId()}.{$production->getIndex()}.{$symbolIndex}";
        switch ($hash) {
            case SymbolType::NT_JSON_PATH . ".0.0":
                var_dump("BUFFER <- []");
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.is_inline_path', false);
                break;

            case SymbolType::NT_PATH . ".0.1":
                $rootName = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $isInlinePath = $production
                    ->getHeader()
                    ->getAttribute('i.is_inline_path');
                $pathType = $this->getPathRootType($rootName, $isInlinePath);
                var_dump("BUFFER <- @" . ($isInlinePath ? "CURRENT" : "ROOT"));
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_type', $pathType)
                    ->setAttribute('i.is_inline_path', $isInlinePath);
                break;

            case SymbolType::NT_BRACKET_FILTER . ".1.0":
                var_dump("BUFFER <- BUFFER.PROPERTY // [string list]");
                break;

            case SymbolType::NT_FILTER_LIST . ".0.1":
                var_dump("BUFFER <- BUFFER.PROPERTY // .name");
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                $filterName = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.filterName', $filterName);
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                $filterName = $production
                    ->getHeader()
                    ->getAttribute('i.filterName');
                var_dump("SKIP IF NOT: NAME == '{$filterName}'");
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.is_inline_path', true);
                break;

            case SymbolType::NT_STRING . ".0.1":
            case SymbolType::NT_STRING . ".1.1":
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.text', '');
                break;

            case SymbolType::NT_STRING_CONTENT . ".0.1":
                $prefix = $production
                    ->getHeader()
                    ->getAttribute('i.text');
                $text = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.text', $prefix . $text);
                break;

            case SymbolType::NT_STRING_CONTENT . ".1.2":
                $prefix = $production
                    ->getHeader()
                    ->getAttribute('i.text');
                $text = $production
                    ->getSymbol(1)
                    ->getAttribute('s.text');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.text', $prefix . $text);
                break;
        }
    }

    /**
     * @param string $name
     * @param bool $isInlinePath
     * @return string
     * @throws Exception
     */
    private function getPathRootType(string $name, bool $isInlinePath): string
    {
        switch ($name) {
            case '$':
                return 'absolute';

            case '@':
                if ($isInlinePath) {
                    return 'relative';
                }
                throw new Exception("Relative paths are allowed only in inline filters");
        }
        throw new Exception("Invalid path root: {$name}");
    }
}
