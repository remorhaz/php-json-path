<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use stdClass;

final class NodeObjectValue implements NodeValueInterface, ObjectValueInterface
{

    private $data;

    private $path;

    private $valueFactory;

    public function __construct(
        stdClass $data,
        PathInterface $path,
        NodeValueFactoryInterface $valueFactory
    ) {
        $this->data = $data;
        $this->path = $path;
        $this->valueFactory = $valueFactory;
    }

    public function createChildIterator(): Iterator
    {
        return $this->createChildGenerator();
    }

    private function createChildGenerator(): Generator
    {
        foreach (get_object_vars($this->data) as $name => $property) {
            $stringName = (string) $name;
            yield $stringName => $this
                ->valueFactory
                ->createValue($property, $this->path->copyWithProperty($stringName));
        }
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
