<?php

namespace Remorhaz\JSON\Path\Runtime;

interface NodeSelectorInterface
{

    public function forkNodeList(VariableInterface $nodeList): VariableInterface;

    public function getKeyList(VariableInterface $nodeList): VariableInterface;

    public function getChildList(VariableInterface $nodeList): VariableInterface;

    public function filterNodeList(VariableInterface $nodeList, VariableInterface $filterMask): VariableInterface;
}
