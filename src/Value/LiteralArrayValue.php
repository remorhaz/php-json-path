<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LiteralArrayValue implements ArrayValueInterface, LiteralValueInterface
{

    private $indexMap;

    private $values;

    public function __construct(IndexMapInterface $indexMap, ValueInterface ...$values)
    {
        $this->indexMap = $indexMap;
        $this->values = $values;
    }

    public function createEventIterator(): Iterator
    {
        return $this->createGenerator();
    }

    private function createGenerator(): Generator
    {
        yield new Event\BeforeArrayEvent($this);

        foreach ($this->values as $index => $value) {
            yield new Event\ElementEvent($index, new Path);
            yield from $value->createEventIterator();
        }
        yield new Event\AfterArrayEvent($this);
    }
}
