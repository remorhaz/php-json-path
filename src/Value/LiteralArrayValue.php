<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use ArrayIterator;
use Iterator;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LiteralArrayValue implements ArrayValueInterface, LiteralValueInterface
{

    private $values;

    public function __construct(ValueInterface ...$values)
    {
        $this->values = $values;
    }

    public function createChildIterator(): Iterator
    {
        return new ArrayIterator($this->values);
    }
}
