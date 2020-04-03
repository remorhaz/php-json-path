<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface MutatorInterface
{

    public function deletePaths(NodeValueInterface $rootNode, PathInterface ...$paths): ?NodeValueInterface;

    public function replacePaths(
        NodeValueInterface $rootNode,
        NodeValueInterface $newNode,
        PathInterface ...$paths
    ): NodeValueInterface;
}
