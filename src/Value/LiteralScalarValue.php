<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Generator;
use function is_scalar;
use Iterator;
use Remorhaz\JSON\Data\Event;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

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

    public function createEventIterator(): Iterator
    {
        return $this->createGenerator();
    }

    private function createGenerator(): Generator
    {
        yield new Event\ScalarEvent($this);
    }
}
