<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEvent;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ScalarValueInterface;

final class NodeScalarValue implements NodeValueInterface, ScalarValueInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path)
    {
        if (null !== $data && !is_scalar($data)) {
            throw new Exception\InvalidNodeDataException($data, $path);
        }
        $this->data = $data;
        $this->path = $path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator();
    }

    private function createGenerator(): Generator
    {
        yield new ScalarEvent($this);
    }
}
