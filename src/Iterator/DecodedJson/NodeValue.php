<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\ElementEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\NodeScalarEvent;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use stdClass;

final class NodeValue implements NodeValueInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator($this->data, $this->path);
    }

    private function createGenerator($data, PathInterface $path): Generator
    {
        if (is_scalar($data) || is_null($data)) {
            yield new NodeScalarEvent($data, $path);
            return;
        }

        if (is_array($data)) {
            yield from $this->iterateArray($data, $path);
            return;
        }

        if ($data instanceof stdClass) {
            yield from $this->iterateObject($data, $path);
            return;
        }

        throw new Exception\InvalidDataException($data, $path);
    }

    private function iterateArray(array $data, PathInterface $path): Generator
    {
        yield new BeforeArrayEvent(new self($data, $path));

        $validIndex = 0;
        foreach ($data as $index => $element) {
            if ($index !== $validIndex++) {
                throw new Exception\InvalidElementKeyException($index, $path);
            }
            yield new ElementEvent($index, $path);
            yield from $this->createGenerator($element, $path->copyWithElement($index));
        }

        yield new AfterArrayEvent(new self($data, $path));
    }

    private function iterateObject(stdClass $data, PathInterface $path): Generator
    {
        yield new BeforeObjectEvent(new self($data, $path));

        foreach (get_object_vars($data) as $name => $property) {
            yield new PropertyEvent($name, $path);
            yield from $this->createGenerator($property, $path->copyWithProperty($name));
        }

        yield new AfterObjectEvent(new self($data, $path));
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}