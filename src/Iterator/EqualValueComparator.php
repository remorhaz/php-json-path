<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class EqualValueComparator implements ValueComparatorInterface
{

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
    }

    public function compare(ValueInterface $leftValue, ValueInterface $rightValue): bool
    {
        if ($leftValue instanceof ScalarValueInterface && $rightValue instanceof ScalarValueInterface) {
            return $this->isScalarEqual($leftValue, $rightValue);
        }

        if ($leftValue instanceof ArrayValueInterface && $rightValue instanceof ArrayValueInterface) {
            return $this->isArrayEqual($leftValue, $rightValue);
        }

        if ($leftValue instanceof ObjectValueInterface && $rightValue instanceof ObjectValueInterface) {
            return $this->isObjectEqual($leftValue, $rightValue);
        }

        return false;
    }

    private function isScalarEqual(ScalarValueInterface $leftValue, ScalarValueInterface $rightValue): bool
    {
        return $leftValue->getData() === $rightValue->getData();
    }

    private function isArrayEqual(ArrayValueInterface $leftValue, ArrayValueInterface $rightValue): bool
    {
        $leftValueIterator = $this->valueIterator->createArrayIterator($leftValue->createIterator());
        $rightValueIterator = $this->valueIterator->createArrayIterator($rightValue->createIterator());

        while ($leftValueIterator->valid()) {
            if (!$rightValueIterator->valid()) {
                return false;
            }
            if (!$this->compare($leftValueIterator->current(), $rightValueIterator->current())) {
                return false;
            }
            $leftValueIterator->next();
            $rightValueIterator->next();
        }
        return !$rightValueIterator->valid();
    }

    private function isObjectEqual(ObjectValueInterface $leftValue, ObjectValueInterface $rightValue): bool
    {
        $leftValueIterator = $this->valueIterator->createObjectIterator($leftValue->createIterator());
        $rightValueIterator = $this->valueIterator->createObjectIterator($rightValue->createIterator());

        $valuesByProperty = [];
        while ($leftValueIterator->valid()) {
            $property = $leftValueIterator->key();
            if (isset($valuesByProperty[$property])) {
                return false;
            }
            $valuesByProperty[$property] = $leftValueIterator->current();
            $leftValueIterator->next();
        }
        while ($rightValueIterator->valid()) {
            $property = $rightValueIterator->key();
            if (!isset($valuesByProperty[$property])) {
                return false;
            }
            if (!$this->compare($valuesByProperty[$property], $rightValueIterator->current())) {
                return false;
            }
            unset($valuesByProperty[$property]);
            $rightValueIterator->next();
        }
        return empty($valuesByProperty);
    }
}
