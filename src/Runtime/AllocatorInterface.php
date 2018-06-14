<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Data\NodeInterface;

interface AllocatorInterface
{

    public function allocateString(string $string): VariableInterface;

    public function allocateStringList(string ...$stringList): VariableInterface;

    public function allocateInt(int $int): VariableInterface;

    public function allocateIntList(int ...$intList): VariableInterface;

    public function allocateBoolList(bool ...$boolList): VariableInterface;

    public function allocateBool(bool $bool): VariableInterface;

    public function allocateNode(NodeInterface $node): VariableInterface;

    public function allocateNodeList(NodeInterface ...$nodeList): VariableInterface;

    public function allocateNull(): VariableInterface;

    public function free(VariableInterface $var);
}
