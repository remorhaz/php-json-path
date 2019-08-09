<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Event\AfterArrayEvent;
use Remorhaz\JSON\Data\Event\BeforeArrayEvent;
use Remorhaz\JSON\Data\Event\ElementEvent;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class NodeArrayValue implements NodeValueInterface, ArrayValueInterface
{

    private $data;

    private $path;

    private $valueFactory;

    private $valueIteratorFactory;

    public function __construct(
        array $data,
        PathInterface $path,
        NodeValueFactoryInterface $valueFactory,
        ValueIteratorFactoryInterface $valueIteratorFactory
    ) {
        $this->data = $data;
        $this->path = $path;
        $this->valueFactory = $valueFactory;
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    public function createChildIterator(): Iterator
    {
        return $this
            ->valueIteratorFactory
            ->createArrayIterator($this->createEventIterator());
    }

    public function createEventIterator(): Iterator
    {
        return $this->createEventGenerator($this->data, $this->path);
    }

    private function createEventGenerator(array $data, PathInterface $path): Generator
    {
        yield new BeforeArrayEvent($this);

        $validIndex = 0;
        foreach ($data as $index => $element) {
            if ($index !== $validIndex++) {
                throw new Exception\InvalidElementKeyException($index, $path);
            }
            yield new ElementEvent($index, $path);
            yield from $this
                ->valueFactory
                ->createValue($element, $path->copyWithElement($index))
                ->createEventIterator();
        }

        yield new AfterArrayEvent($this);
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
