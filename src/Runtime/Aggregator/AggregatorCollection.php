<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

final class AggregatorCollection implements AggregatorCollectionInterface
{
    private const MIN = 'min';
    private const MAX = 'max';
    private const LENGTH = 'length';
    private const AVG = 'avg';
    private const STDDEV = 'stddev';

    public function byName(string $name): ValueAggregatorInterface
    {
        return match ($name) {
            self::MIN => new MinAggregator(),
            self::MAX => new MaxAggregator(),
            self::LENGTH => new LengthAggregator(),
            self::AVG => new AvgAggregator(),
            self::STDDEV => new StdDevAggregator(),
            default => throw new Exception\AggregateFunctionNotFoundException($name),
        };
    }
}
