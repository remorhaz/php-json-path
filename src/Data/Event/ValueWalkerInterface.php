<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Iterator;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Processor\Mutator\MutationInterface;

interface ValueWalkerInterface
{

    public function createEventIterator(NodeValueInterface $value, PathInterface $path): Iterator;

    public function createMutableEventIterator(
        NodeValueInterface $value,
        PathInterface $path,
        MutationInterface $modifier
    ): Iterator;
}
