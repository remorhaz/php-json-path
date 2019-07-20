<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;

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
                return new MinAggregator($this->valueIteratorFactory);

            case self::MAX:
                return new MaxAggregator($this->valueIteratorFactory);

            case self::LENGTH:
                return new LengthAggregator($this->valueIteratorFactory);

            case self::AVG:
                return new AvgAggregator($this->valueIteratorFactory);

            case self::STDDEV:
                return new StdDevAggregator($this->valueIteratorFactory);
        }

        throw new Exception\AggregateFunctionNotFoundException($name);
    }
}
