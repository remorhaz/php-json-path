<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function iterator_count;

final class LengthAggregator implements ValueAggregatorInterface
{

    public function tryAggregate(ValueInterface $value): ?ValueInterface
    {
        if (!$value instanceof ArrayValueInterface) {
            return null;
        }
        $length = iterator_count($value->createChildIterator());

        return new LiteralScalarValue($length);
    }
}
