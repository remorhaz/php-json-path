<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Data\NodeInterface;

interface RuntimeInterface
{

    public function getAllocator(): AllocatorInterface;

    public function getCalculator(): CalculatorInterface;

    public function getDocumentRoot(): NodeInterface;

    public function getNodeSelector(): NodeSelectorInterface;
}
