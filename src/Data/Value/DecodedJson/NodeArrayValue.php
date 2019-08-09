<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class NodeArrayValue implements NodeValueInterface, ArrayValueInterface
{

    private $data;

    private $path;

    private $valueFactory;

    public function __construct(
        array $data,
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
        $validIndex = 0;
        foreach ($this->data as $index => $element) {
            if ($index !== $validIndex++) {
                throw new Exception\InvalidElementKeyException($index, $this->path);
            }
            yield $index => $this
                ->valueFactory
                ->createValue($element, $this->path->copyWithElement($index));
        }
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
