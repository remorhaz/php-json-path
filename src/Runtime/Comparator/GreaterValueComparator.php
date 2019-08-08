<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Comparator;

use Collator;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use function is_float;
use function is_int;
use function is_string;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class GreaterValueComparator implements ComparatorInterface
{

    private $valueIteratorFactory;

    private $collator;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory, Collator $collator)
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
        if ((is_int($leftData) || is_float($leftData)) && (is_int($rightData) || is_float($rightData))) {
            return $leftData > $rightData;
        }

        if (is_string($leftData) && is_string($rightData)) {
            return 1 === $this->collator->compare($leftData, $rightData);
        }

        return false;
    }
}
