<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;

final class AggregatorCollection
{

    private const MIN = 'min';
    private const MAX = 'max';
    private const LENGTH = 'length';
    private const AVG = 'avg';
    private const STDDEV = 'stddev';

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    public function byName(string $name): ValueAggregatorInterface
    {
        switch ($name) {
            case self::MIN:
                return $this->min();

            case self::MAX:
                return $this->max();

            case self::LENGTH:
                return $this->length();

            case self::AVG:
                return $this->avg();

            case self::STDDEV:
                return $this->stdDev();
        }

        throw new Exception\AggregateFunctionNotFound($name);
    }

    public function min(): ValueAggregatorInterface
    {
        return new MinAggregator($this->valueIteratorFactory);
    }

    public function max(): ValueAggregatorInterface
    {
        return new MaxAggregator($this->valueIteratorFactory);
    }

    public function length(): ValueAggregatorInterface
    {
        return new LengthAggregator($this->valueIteratorFactory);
    }

    public function avg(): ValueAggregatorInterface
    {
        return new AvgAggregator($this->valueIteratorFactory);
    }

    public function stdDev(): ValueAggregatorInterface
    {
        return new StdDevAggregator($this->valueIteratorFactory);
    }
}
