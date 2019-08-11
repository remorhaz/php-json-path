<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Iterator;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Processor\Mutator\MutationInterface;

final class ValueWalker implements ValueWalkerInterface
{

    public function createEventIterator(NodeValueInterface $value, PathInterface $path): Iterator
    {
        return (new EventGenerator($value, $path))();
    }

    public function createMutableEventIterator(
        NodeValueInterface $value,
        PathInterface $path,
        MutationInterface $modifier
    ): Iterator {
        foreach ($this->createEventIterator($value, $path) as $event) {
            yield from $modifier($event);
        }
    }
}
