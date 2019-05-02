<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Generator;
use function is_scalar;
use Iterator;

final class LiteralValue implements LiteralValueInterface
{

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function createIterator(): Iterator
    {
        return $this->createGenerator($this->data);
    }

    private function createGenerator($data): Generator
    {
        if (null === $data || is_scalar($data)) {
            yield new Event\LiteralScalarEvent($data);
            return;
        }

        throw new Exception\InvalidDataException($data);
    }
}
