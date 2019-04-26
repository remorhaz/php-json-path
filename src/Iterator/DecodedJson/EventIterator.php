<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Generator;
use function is_array;
use function is_null;
use function is_scalar;
use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\ElementEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\ScalarEvent;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use stdClass;

final class EventIterator
{

    private $data;

    private $path;

    public static function create($data, PathInterface $path): Iterator
    {
        $iterator = new self($data, $path);

        return new class($iterator) implements Iterator
        {

            private $iterator;

            private $generator;

            public function __construct(EventIterator $iterator)
            {
                $this->iterator = $iterator;
            }

            public function current()
            {
                return $this->getGenerator()->current();
            }

            public function key()
            {
                return $this->getGenerator()->key();
            }

            public function next()
            {
                $this->getGenerator()->next();
            }

            public function valid()
            {
                return $this->getGenerator()->valid();
            }

            public function rewind()
            {
                $this->createGenerator()->rewind();
            }

            private function getGenerator(): Generator
            {
                return $this->generator ?? $this->createGenerator();
            }

            private function createGenerator(): Generator
            {
                $this->generator = $this->iterator->getGenerator();

                return $this->generator;
            }
        };
    }

    private function __construct($data, PathInterface $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    public function getGenerator(): Generator
    {
        if (is_scalar($this->data) || is_null($this->data)) {
            yield new ScalarEvent($this->data, $this->path);
            return;
        }

        if (is_array($this->data)) {
            yield from $this->iterateArray($this->data);
            return;
        }

        if ($this->data instanceof stdClass) {
            yield from $this->iterateObject($this->data);
            return;
        }

        throw new Exception\InvalidDataException($this->data, $this->path);
    }

    private function iterateArray(array $data): Generator
    {
        yield new BeforeArrayEvent(self::create($data, $this->path), $this->path);

        $validIndex = 0;
        foreach ($data as $index => $element) {
            if ($index !== $validIndex++) {
                throw new Exception\InvalidElementKeyException($index, $this->path);
            }
            yield new ElementEvent($index, $this->path);
            yield from $element instanceof \Iterator
                ? $element
                : self::create($element, $this->path->copyWithElement($index));
        }

        yield new AfterArrayEvent(self::create($data, $this->path), $this->path);
    }

    private function iterateObject(stdClass $data): Generator
    {
        yield new BeforeObjectEvent(self::create($data, $this->path), $this->path);

        foreach (get_object_vars($data) as $name => $property) {
            yield new PropertyEvent($name, $this->path);
            yield from self::create($property, $this->path->copyWithProperty($name));
        }

        yield new AfterObjectEvent(self::create($data, $this->path), $this->path);
    }
}
