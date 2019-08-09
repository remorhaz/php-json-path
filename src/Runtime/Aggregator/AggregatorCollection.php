<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

final class AggregatorCollection
{

    private const MIN = 'min';
    private const MAX = 'max';
    private const LENGTH = 'length';
    private const AVG = 'avg';
    private const STDDEV = 'stddev';

    public function byName(string $name): ValueAggregatorInterface
    {
        switch ($name) {
            case self::MIN:
                return new MinAggregator;

            case self::MAX:
                return new MaxAggregator;

            case self::LENGTH:
                return new LengthAggregator;

            case self::AVG:
                return new AvgAggregator;

            case self::STDDEV:
                return new StdDevAggregator;
        }

        throw new Exception\AggregateFunctionNotFoundException($name);
    }
}
