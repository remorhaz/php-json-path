<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    private $pathBufferList = [];

    private $varList = [];

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
                $pathBufferId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.path_buffer_id');
                var_dump("RETURN BUFFER({$pathBufferId})");
                break;

            case SymbolType::NT_PATH . ".0":
                $pathBufferId = $production
                    ->getSymbol(1)
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $function = $production
                    ->getHeader()
                    ->getAttribute('i.filter_name');
                var_dump("BUFFER({$pathBufferId}) = BUFFER({$pathBufferId}).{$function}()");
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0":
                $varId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $inlinePathBufferId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.path_buffer_id');
                $varId = $this->createVar();
                var_dump("BUFFER({$pathBufferId}).VAR({$varId}) <- BUFFER({$inlinePathBufferId})");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $int = $production
                    ->getSymbol(0)
                    ->getAttribute('s.int');
                $varId = $this->createVar();
                var_dump("BUFFER({$pathBufferId}).VAR({$varId}) <- INT({$int})");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
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

            case SymbolType::NT_STRING_NEXT . ".0":
                $textList = $production
                    ->getSymbol(4)
                    ->getAttribute('s.text_list');
                $production
                    ->getHeader()
                    ->setAttribute('s.text_list', $textList);
                break;

            case SymbolType::NT_STRING_NEXT . ".1":
                $textList = $production
                    ->getHeader()
                    ->getAttribute('i.text_list');
                $production
                    ->getHeader()
                    ->setAttribute('s.text_list', $textList);
                break;

            case SymbolType::NT_STRING_LIST . ".0":
                $textList = $production
                    ->getSymbol(2)
                    ->getAttribute('s.text_list');
                $production
                    ->getHeader()
                    ->setAttribute('s.text_list', $textList);
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
                    ->getAttribute('s.text');
                $production
                    ->getHeader()
                    ->setAttribute('s.text', $text);
                break;

            case SymbolType::NT_STRING_CONTENT . ".1":
                $text = $production
                    ->getSymbol(2)
                    ->getAttribute('s.text');
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
                $textList = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text_list');
                if (count($textList) == 1) {
                    $text = array_pop($textList);
                    var_dump("SKIP IF NOT: KEY == '{$text}'");
                    break;
                }
                var_dump("SKIP IF NOT: OR");
                foreach ($textList as $text) {
                    var_dump(" - OR: KEY == '{$text}'");
                }
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(2)
                    ->getAttribute('s.var_id');
                var_dump("BUFFER({$pathBufferId}).FILTER_KEY BUFFER({$pathBufferId}).VAR({$varId})");
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(3)
                    ->getAttribute('s.var_id');
                var_dump("BUFFER({$pathBufferId}).FILTER BUFFER({$pathBufferId}).VAR({$varId})");
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(1)
                    ->getAttribute('s.var_id');
                var_dump("BUFFER({$pathBufferId}).VAR({$varId}) = NOT(VAR({$varId}))");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1":
                $varId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0":
                $leftVarId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $rightVarId = $production
                    ->getSymbol(2)
                    ->getAttribute('s.var_id');
                $varId = $this->createVar();
                var_dump("VAR({$varId}) = VAR({$leftVarId}) EQ VAR({$rightVarId})");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".8":
                $varId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0":
                $varId = $production
                    ->getSymbol(1)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0":
                $leftVarId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $rightVarId = $production
                    ->getSymbol(2)
                    ->getAttribute('s.var_id');
                $varId = $this->createVar();
                var_dump("VAR({$varId}) = VAR({$leftVarId}) AND VAR({$rightVarId})");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".1":
                $varId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0":
                $varId = $production
                    ->getSymbol(1)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0":
                $leftVarId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $rightVarId = $production
                    ->getSymbol(2)
                    ->getAttribute('s.var_id');
                $varId = $this->createVar();
                var_dump("VAR({$varId}) = VAR({$leftVarId}) OR VAR({$rightVarId})");
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".1":
                $varId = $production
                    ->getHeader()
                    ->getAttribute('i.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR . ".0":
                $varId = $production
                    ->getSymbol(1)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_GROUP . ".0":
                $varId = $production
                    ->getSymbol(2)
                    ->getAttribute('s.var_id');
                $production
                    ->getHeader()
                    ->setAttribute('s.var_id', $varId);
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
                $pathBufferId = $this->createPathBuffer();
                if ($isInlinePath) {
                    $parentPathBufferId = $production
                        ->getHeader()
                        ->getAttribute('i.path_buffer_id');
                    var_dump("BUFFER({$pathBufferId}) <- BUFFER({$parentPathBufferId})");
                } else {
                    var_dump("BUFFER({$pathBufferId}) <- @ROOT");
                }
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
                    ->setAttribute('i.path_type', $pathType)
                    ->setAttribute('i.is_inline_path', $isInlinePath);
                break;

            case SymbolType::NT_BRACKET_FILTER . ".1.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                var_dump("BUFFER({$pathBufferId}) = BUFFER({$pathBufferId}).PROPERTY // [string list]");
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5.3":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(3)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR . ".0.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR . ".0.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.var_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
                    ->setAttribute('i.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.var_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
                    ->setAttribute('i.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $varId = $production
                    ->getSymbol(0)
                    ->getAttribute('s.var_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
                    ->setAttribute('i.var_id', $varId);
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".2.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".3.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".6.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".7.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_GROUP . ".0.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_STRING_LIST . ".0.2":
                $text = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $textList = [$text];
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.text_list', $textList);
                break;

            case SymbolType::NT_STRING_NEXT . ".0.4":
                $textList = $production
                    ->getHeader()
                    ->getAttribute('i.text_list');
                $text = $production
                    ->getSymbol(2)
                    ->getAttribute('s.text');
                $textList[] = $text;
                $production
                    ->getSymbol(4)
                    ->setAttribute('i.text_list', $textList);
                break;

            case SymbolType::NT_FILTER_LIST . ".0.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_FILTER_LIST . ".1.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(2)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_FILTER_LIST . ".2.4":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(4)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                $filterName = $production
                    ->getSymbol(0)
                    ->getAttribute('s.text');
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
                    ->setAttribute('i.filter_name', $filterName);
                break;

            case SymbolType::NT_DOT_FILTER . ".1.1":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(1)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                $filterName = $production
                    ->getHeader()
                    ->getAttribute('i.filter_name');
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                var_dump("BUFFER({$pathBufferId}) = BUFFER({$pathBufferId}).NEXT_KEY '{$filterName}' // .name");
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId);
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                $pathBufferId = $production
                    ->getHeader()
                    ->getAttribute('i.path_buffer_id');
                $production
                    ->getSymbol(0)
                    ->setAttribute('i.path_buffer_id', $pathBufferId)
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

    private function createPathBuffer(): int
    {
        $id = count($this->pathBufferList);
        $this->pathBufferList[$id] = [];
        return $id;
    }

    private function createVar(): int
    {
        $id = count($this->varList);
        $this->varList[] = $id;
        return $id;
    }
}
