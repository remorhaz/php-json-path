<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Generator;
use Iterator;

final class LiteralArrayValue implements ArrayValueInterface, LiteralValueInterface
{

    private $indexMap;

    private $values;

    public function __construct(IndexMapInterface $indexMap, ValueInterface ...$values)
    {
        $this->indexMap = $indexMap;
        $this->values = $values;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator();
    }

    private function createGenerator(): Generator
    {
        yield new Event\BeforeArrayEvent($this);

        foreach ($this->values as $index => $value) {
            yield new Event\ElementEvent($index, Path::createEmpty());
            yield from $value->createIterator();
        }
        yield new Event\AfterArrayEvent($this);
    }

}
