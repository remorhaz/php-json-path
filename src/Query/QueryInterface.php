<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

interface QueryInterface
{

    /**
     * @param NodeValueInterface $rootNode
     * @param RuntimeInterface   $runtime
     * @return ValueListInterface
     * @throws Exception\ExceptionInterface
     */
    public function __invoke(NodeValueInterface $rootNode, RuntimeInterface $runtime): ValueListInterface;

    public function getCapabilities(): CapabilitiesInterface;

    public function getSource(): string;
}
