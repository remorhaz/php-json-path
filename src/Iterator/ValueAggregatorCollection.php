<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class ValueAggregatorCollection
{

    private const MIN = 'min';
    private const MAX = 'max';
    private const LENGTH = 'length';

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
}
