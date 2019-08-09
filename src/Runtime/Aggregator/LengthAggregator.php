<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use function iterator_count;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LengthAggregator implements ValueAggregatorInterface
{

    public function tryAggregate(ValueInterface $value): ?ValueInterface
    {
        $length = $value instanceof ArrayValueInterface
            ? iterator_count($value->createChildIterator())
            : null;

        return isset($length)
            ? new LiteralScalarValue($length)
            : null;
    }
}
