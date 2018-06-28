<?php

namespace Remorhaz\JSON\Path\Runtime;

class NodeSelector implements NodeSelectorInterface
{

    private $runtime;

    public function __construct(RuntimeInterface $runtime)
    {
        $this->runtime = $runtime;
    }

    public function filterNodeList(VariableInterface $nodeList, VariableInterface $filterMask): VariableInterface
    {
    }

    public function forkNodeList(VariableInterface $nodeList): VariableInterface
    {
    }

    public function getChildList(VariableInterface $nodeList): VariableInterface
    {
    }

    public function getKeyList(VariableInterface $nodeList): VariableInterface
    {

    }
}
