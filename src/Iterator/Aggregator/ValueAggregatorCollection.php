<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Aggregator;

use Remorhaz\JSON\Path\Iterator\Exception;
use Remorhaz\JSON\Path\Iterator\ValueIterator;

final class ValueAggregatorCollection
{

    private const MIN = 'min';
    private const MAX = 'max';
    private const LENGTH = 'length';
    private const AVG = 'avg';
    private const STDDEV = 'stddev';

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
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
        return new MinAggregator($this->valueIterator);
    }

    public function max(): ValueAggregatorInterface
    {
        return new MaxAggregator($this->valueIterator);
    }

    public function length(): ValueAggregatorInterface
    {
        return new LengthAggregator($this->valueIterator);
    }

    public function avg(): ValueAggregatorInterface
    {
        return new AvgAggregator($this->valueIterator);
    }

    public function stdDev(): ValueAggregatorInterface
    {
        return new StdDevAggregator($this->valueIterator);
    }
}
