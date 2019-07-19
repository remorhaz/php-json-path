<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

use Generator;
use function is_scalar;
use Iterator;

final class LiteralScalarValue implements LiteralValueInterface, ScalarValueInterface
{

    private $data;

    public function __construct($data)
    {
        if (null !== $data && !is_scalar($data)) {
            throw new Exception\InvalidScalarDataException($data);
        }
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator();
    }

    private function createGenerator(): Generator
    {
        yield new Event\ScalarEvent($this);
    }
}
