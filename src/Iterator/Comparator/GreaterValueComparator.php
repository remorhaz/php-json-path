<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Comparator;

use Collator;
use function is_int;
use function is_string;
use Remorhaz\JSON\Path\Iterator\ScalarValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;

final class GreaterValueComparator implements ValueComparatorInterface
{

    private $valueIteratorFactory;

    private $collator;

    public function __construct(ValueIteratorFactory $valueIteratorFactory, Collator $collator)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->collator = $collator;
    }

    public function compare(ValueInterface $leftValue, ValueInterface $rightValue): bool
    {
        if ($leftValue instanceof ScalarValueInterface && $rightValue instanceof ScalarValueInterface) {
            return $this->isScalarGreater($leftValue, $rightValue);
        }

        return false;
    }

    private function isScalarGreater(ScalarValueInterface $leftValue, ScalarValueInterface $rightValue): bool
    {
        $leftData = $leftValue->getData();
        $rightData = $rightValue->getData();
        if (is_int($leftData) && is_int($rightData)) {
            return $leftData > $rightData;
        }

        if (is_string($leftData) && is_string($rightData)) {
            return 1 === $this->collator->compare($leftData, $rightData);
        }

        return false;
    }
}