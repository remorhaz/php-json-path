<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Data\NodeInterface;

class Runtime implements RuntimeInterface
{

    private $documentRoot;

    private $allocator;

    private $calculator;

    private $nodeSelector;

    public function __construct(NodeInterface $documentRoot)
    {
        $this->documentRoot = $documentRoot;
    }

    public function getDocumentRoot(): NodeInterface
    {
        return $this->documentRoot;
    }

    public function getAllocator(): AllocatorInterface
    {
        if (!isset($this->allocator)) {
            $this->allocator = new Allocator;
        }
        return $this->allocator;
    }

    public function getCalculator(): CalculatorInterface
    {
        if (!isset($this->calculator)) {
            $this->calculator = new Calculator;
        }
        return $this->calculator;
    }

    public function getNodeSelector(): NodeSelectorInterface
    {
        if (!isset($this->nodeSelector)) {
            $this->nodeSelector = new NodeSelector($this);
        }
        return $this->nodeSelector;
    }
}
