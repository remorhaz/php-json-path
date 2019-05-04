<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\ObjectValueInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use stdClass;

final class NodeObjectValue implements NodeValueInterface, ObjectValueInterface
{

    private $data;

    private $path;

    private $valueFactory;

    public function __construct(stdClass $data, PathInterface $path, NodeValueFactory $valueFactory)
    {
        $this->data = $data;
        $this->path = $path;
        $this->valueFactory = $valueFactory;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator($this->data, $this->path);
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    private function createGenerator(stdClass $data, PathInterface $path): Generator
    {
        yield new BeforeObjectEvent($this);

        foreach (get_object_vars($data) as $name => $property) {
            yield new PropertyEvent($name, $path);
            yield from $this
                ->valueFactory
                ->createValue($property, $path->copyWithProperty($name))
                ->createIterator();
        }

        yield new AfterObjectEvent($this);
    }
}
