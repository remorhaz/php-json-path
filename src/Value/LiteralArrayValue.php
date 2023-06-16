<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use ArrayIterator;
use Iterator;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_values;

final class LiteralArrayValue implements ArrayValueInterface, LiteralValueInterface
{
    /**
     * @var list<ValueInterface>
     */
    private array $values;

    public function __construct(ValueInterface ...$values)
    {
        $this->values = array_values($values);
    }

    /**
     * @return Iterator<int, ValueInterface>
     */
    public function createChildIterator(): Iterator
    {
        return new ArrayIterator($this->values);
    }
}
