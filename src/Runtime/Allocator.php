<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Data\NodeInterface;

class Allocator implements AllocatorInterface
{

    public function allocateBool(bool $bool): VariableInterface
    {
        return new ScalarVariable(VariableInterface::TYPE_BOOL, $bool);
    }

    public function allocateBoolList(bool ...$boolList): VariableInterface
    {
        $varList = [];
        foreach ($boolList as $bool) {
            $varList[] = $this->allocateBool($bool);
        }
        return new ListVariable(VariableInterface::TYPE_BOOL, ...$varList);
    }

    public function allocateInt(int $int): VariableInterface
    {
        return new ScalarVariable(VariableInterface::TYPE_INT, $int);
    }

    public function allocateIntList(int ...$intList): VariableInterface
    {
        $varList = [];
        foreach ($intList as $int) {
            $varList[] = $this->allocateInt($int);
        }
        return new ListVariable(VariableInterface::TYPE_INT, ...$varList);
    }

    public function allocateNull(): VariableInterface
    {
        return new ScalarVariable(VariableInterface::TYPE_NULL, null);
    }

    public function allocateString(string $string): VariableInterface
    {
        return new ScalarVariable(VariableInterface::TYPE_STRING, $string);
    }

    public function allocateStringList(string ...$stringList): VariableInterface
    {
        $varList = [];
        foreach ($stringList as $string) {
            $varList[] = $this->allocateString($string);
        }
        return new ListVariable(VariableInterface::TYPE_STRING, ...$varList);
    }

    public function allocateNode(NodeInterface $node): VariableInterface
    {
        return new ScalarVariable(VariableInterface::TYPE_NODE, $node);
    }

    public function allocateNodeList(NodeInterface ...$nodeList): VariableInterface
    {
        $varList = [];
        foreach ($nodeList as $node) {
            $varList[] = $this->allocateNode($node);
        }
        return new ListVariable(VariableInterface::TYPE_STRING, ...$varList);
    }
}
