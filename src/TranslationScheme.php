<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;

class TranslationScheme implements TranslationSchemeInterface
{

    private $varList = [];

    private $unsetVarList = [];

    private $codeLineList = [];

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
                $pathId = $symbols[0]['s.path_id'];
                $this->addCodeLine("return \$var{$pathId};");
                break;

            case SymbolType::NT_PATH . ".0":
                $header['s.path_id'] = $symbols[1]['s.path_id'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".0":
                $pathId = $header['i.path_id'];
                $function = var_export($header['i.filter_name'], true);
                $this->addCodeLine("// data aggregate function");
                $functionVarId = $this->createVar();
                $this->addCodeLine("\$var{$functionVarId} = \$allocator->allocateString({$function});");
                $varId = $this->createVar();
                $this->addCodeLine("\$var{$varId} = \$nodeSelector->aggregate(\$var{$pathId}, \$var{$functionVarId});");
                $this->unsetVar($pathId, $functionVarId);
                $header['s.path_id'] = $varId;
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1":
                $header['s.path_id'] = $symbols[0]['s.path_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0":
                $header['s.path_id'] = $symbols[1]['s.path_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1":
                $header['s.path_id'] = $symbols[1]['s.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".0":
                $header['s.path_id'] = $symbols[1]['s.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".1":
                $header['s.path_id'] = $symbols[2]['s.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2":
                $header['s.path_id'] = $symbols[4]['s.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".3":
                $header['s.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0":
                $header['s.var_id'] = $symbols[0]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1":
                $header['s.var_id'] = $symbols[0]['s.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".2":
                $int = $symbols[0]['s.int'];
                $varId = $this->createVar();
                $this->addCodeLine("\$var{$varId} = \$allocator->allocateInt({$int});");
                $header['s.var_id'] = $varId;
                break;

            case SymbolType::NT_INT . ".0":
                $header['s.int'] = -$symbols[1]['s.int'];
                break;

            case SymbolType::NT_INT . ".1":
                $header['s.int'] = $symbols[0]['s.int'];
                break;

            case SymbolType::NT_STRING_NEXT . ".0":
                $header['s.text_list_id'] = $symbols[4]['s.text_list_id'];
                break;

            case SymbolType::NT_STRING_NEXT . ".1":
                $header['s.text_list_id'] = $header['i.text_list_id'];
                break;

            case SymbolType::NT_STRING_LIST . ".0":
                $header['s.text_list_id'] = $symbols[2]['s.text_list_id'];
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

            case SymbolType::NT_BRACKET_FILTER . ".1":
                $pathId = $header['i.path_id'];
                $filterId = $symbols[0]['i.filter_id'];
                $textListId = $symbols[0]['s.text_list_id'];
                $matchId = $this->createVar();
                $this->addCodeLine("\$var{$matchId} = \$calculator->in(\$var{$filterId}, \$var{$textListId});");
                $this->unsetVar($textListId);
                $this->unsetVar($filterId);
                $dataId = $this->createVar();
                $this->addCodeLine("\$var{$dataId} = \$nodeSelector->getChildList(\$var{$pathId});");
                $this->unsetVar($pathId);
                $newPathId = $this->createVar();
                $this->addCodeLine("\$var{$newPathId} = \$nodeSelector->filterNodeList(\$var{$dataId}, \$var{$matchId});");
                $this->unsetVar($matchId);
                $header['s.path_id'] = $newPathId;
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4":
                $pathId = $header['i.path_id'];
                $varId = $symbols[2]['s.var_id'];
                $this->addCodeLine("// filter by key");
                $keysId = $this->createVar();
                $this->addCodeLine("\$var{$keysId} = \$nodeSelector->getKeyList(\$var{$pathId});");
                $matchId = $this->createVar();
                $this->addCodeLine("\$var{$matchId} = \$calculator->in(\$var{$keysId}, \$var{$varId});");
                $this->unsetVar($varId, $keysId);
                $dataId = $this->createVar();
                $this->addCodeLine("\$var{$dataId} = \$nodeSelector->getChildList(\$var{$pathId});");
                $this->unsetVar($pathId);
                $newPathId = $this->createVar();
                $this->addCodeLine("\$var{$newPathId} = \$nodeSelector->filterNodeList(\$var{$dataId}, \$var{$matchId});");
                $this->unsetVar($matchId, $dataId);
                $header['s.path_id'] = $newPathId;
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5":
                $pathId = $header['i.path_id'];
                $varId = $symbols[3]['s.var_id'];
                $this->addCodeLine("// filter by bool");
                $newPathId = $this->createVar();
                $this->addCodeLine("\$var{$newPathId} = \$nodeSelector->filterNodeList(\$var{$pathId}, \$var{$varId});");
                $this->unsetVar($pathId, $varId);
                $header['s.path_id'] = $newPathId;
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0":
                $varId = $symbols[1]['s.var_id'];
                $this->addCodeLine("// NOT expression");
                $newVarId = $this->createVar();
                $this->addCodeLine("\$var{$newVarId} = \$calculator->not(\$var{$varId});");
                $this->unsetVar($varId);
                $header['s.var_id'] = $newVarId;
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1":
                $header['s.var_id'] = $symbols[0]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0":
                $leftVarId = $header['i.var_id'];
                $rightVarId = $symbols[2]['s.var_id'];
                $this->addCodeLine("// EQ expression");
                $varId = $this->createVar();
                $this->addCodeLine("\$var{$varId} = \$calculator->eq(\$var{$leftVarId}, \$var{$rightVarId});");
                $this->unsetVar($leftVarId, $rightVarId);
                $header['s.var_id'] = $varId;
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".8":
                $header['s.var_id'] = $header['i.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0":
                $header['s.var_id'] = $symbols[1]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0":
                $leftVarId = $header['i.var_id'];
                $rightVarId = $symbols[2]['s.var_id'];
                $this->addCodeLine("// AND expression");
                $varId = $this->createVar();
                $this->addCodeLine("\$var{$varId} = \$calculator->and(\$var{$leftVarId}, \$var{$rightVarId})");
                $this->unsetVar($leftVarId, $rightVarId);
                $header['s.var_id'] = $varId;
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".1":
                $header['s.var_id'] = $header['i.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0":
                $header['s.var_id'] = $symbols[1]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0":
                $leftVarId = $header['i.var_id'];
                $rightVarId = $symbols[2]['s.var_id'];
                $this->addCodeLine("// OR expression");
                $varId = $this->createVar();
                $this->addCodeLine("\$var{$varId} = \$calculator->or(\$var{$leftVarId}, \$var{$rightVarId});");
                $this->unsetVar($leftVarId, $rightVarId);
                $header['s.var_id'] = $varId;
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".1":
                $header['s.var_id'] = $header['i.var_id'];
                break;

            case SymbolType::NT_EXPR . ".0":
                $header['s.var_id'] = $symbols[1]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_GROUP . ".0":
                $header['s.var_id'] = $symbols[2]['s.var_id'];
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
                $this->addCodeLine("\$allocator = \$runtime->getAllocator();");
                $this->addCodeLine("\$calculator = \$runtime->getCalculator();");
                $this->addCodeLine("\$nodeSelector = \$runtime->getNodeSelector();");
                break;

            case SymbolType::NT_PATH . ".0.1":
                $rootName = $symbols[0]['s.text'];
                $isInlinePath = $header['i.is_inline_path'];
                $pathType = $this->getPathRootType($rootName, $isInlinePath);
                $pathId = $this->createVar();
                if ($isInlinePath) {
                    $parentPathId = $header['i.path_id'];
                    $this->addCodeLine("// init inline path");
                    $this->addCodeLine("\$var{$pathId} = \$nodeSelector->forkNodeList(\$var{$parentPathId});");
                } else {
                    $this->addCodeLine("// init root path");
                    $this->addCodeLine("\$var{$pathId} = \$allocator->allocateNodeList(\$runtime->getDocumentRoot());");
                }
                $symbols[1]['i.path_id'] = $pathId;
                $symbols[1]['i.path_type'] = $pathType;
                $symbols[1]['i.is_inline_path'] = $isInlinePath;
                break;

            case SymbolType::NT_BRACKET_FILTER . ".1.0":
                $pathId = $header['i.path_id'];
                $this->addCodeLine("// [string list]");
                $filterId = $this->createVar();
                $this->addCodeLine("\$var{$filterId} = \$nodeSelector->getKeyList(\$var{$pathId});");
                $symbols[0]['i.filter_id'] = $filterId;
                break;

            case SymbolType::NT_BRACKET_FILTER . ".4.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_BRACKET_FILTER . ".5.3":
                $symbols[3]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR . ".0.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                $symbols[1]['i.var_id'] = $symbols[0]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                $symbols[1]['i.var_id'] = $symbols[0]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_OR_TAIL . ".0.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                $symbols[1]['i.var_id'] = $symbols[0]['s.var_id'];
                break;

            case SymbolType::NT_EXPR_ARG_AND_TAIL . ".0.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP . ".1.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".0.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".2.0":
            case SymbolType::NT_EXPR_ARG_SCALAR . ".3.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".0.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".1.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".2.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".3.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".4.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".5.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".6.2":
            case SymbolType::NT_EXPR_ARG_COMP_TAIL . ".7.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_EXPR_GROUP . ".0.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_STRING_LIST . ".0.2":
                $text = var_export($symbols[0]['s.text'], true);
                $textListId = $this->createVar();
                $this->addCodeLine("\$var{$textListId} = \$allocator->allocateStringList({$text});");
                $symbols[2]['i.text_list_id'] = $textListId;
                break;

            case SymbolType::NT_STRING_NEXT . ".0.4":
                $textListId = $header['i.text_list_id'];
                $text = var_export($symbols[2]['s.text'], true);
                $textId = $this->createVar();
                $this->addCodeLine("\$var{$textId} = \$allocator->allocateString({$text});");
                $this->addCodeLine("\$var{$textListId}->append(\$var{$textId});");
                $symbols[4]['i.text_list_id'] = $textListId;
                break;

            case SymbolType::NT_FILTER_LIST . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".1.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2.2":
                $symbols[2]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_FILTER_LIST . ".2.4":
                $symbols[4]['i.path_id'] = $symbols[2]['s.path_id'];
                break;

            case SymbolType::NT_DOT_FILTER . ".0.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                $symbols[1]['i.filter_name'] = $symbols[0]['s.text'];
                break;

            case SymbolType::NT_DOT_FILTER . ".1.1":
                $symbols[1]['i.path_id'] = $header['i.path_id'];
                break;

            case SymbolType::NT_DOT_FILTER_NEXT . ".1.0":
                $filterName = var_export($header['i.filter_name'], true);
                $pathId = $header['i.path_id'];
                $stringId = $this->createVar();
                $this->addCodeLine("// .name");
                $this->addCodeLine("\$var{$stringId} = \$allocator->allocateString({$filterName});");
                $keysId = $this->createVar();
                $this->addCodeLine("\$var{$keysId} = \$nodeSelector->getKeyList(\$var{$pathId});");
                $matchId = $this->createVar();
                $this->addCodeLine("\$var{$matchId} = \$calculator->eq(\$var{$stringId}, \$var{$keysId});");
                $this->unsetVar($stringId, $keysId);
                $dataId = $this->createVar();
                $this->addCodeLine("\$var{$dataId} = \$nodeSelector->getChildList(\$var{$pathId});");
                $this->unsetVar($pathId);
                $newPathId = $this->createVar();
                $this->addCodeLine("\$var{$newPathId} = \$nodeSelector->filterNodeList(\$var{$dataId}, \$var{$matchId});");
                $this->unsetVar($dataId, $matchId);
                $symbols[0]['i.path_id'] = $newPathId;
                break;

            case SymbolType::NT_EXPR_ARG_SCALAR . ".1.0":
                $symbols[0]['i.path_id'] = $header['i.path_id'];
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

    private function createVar(): int
    {
        if (!empty($this->unsetVarList)) {
            return array_shift($this->unsetVarList);
        }
        $id = count($this->varList);
        $this->varList[] = $id;
        return $id;
    }

    private function unsetVar(int ...$idList)
    {
        foreach ($idList as $id) {
            //$this->addCodeLine("\$allocator->free(\$var{$id});");
            $this->unsetVarList[] = $id;
            sort($this->unsetVarList);
        }
    }

    private function addCodeLine(string $codeLine)
    {
        $this->codeLineList[] = $codeLine;
        var_dump($codeLine);
    }
}
