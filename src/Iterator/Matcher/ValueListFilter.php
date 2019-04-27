<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use function is_bool;
use function is_object;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueList;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

class ValueListFilter implements ValueListFilterInterface
{

    private $filterValueList;

    public function __construct(ValueListInterface $filterValueList)
    {
        $this->filterValueList = $filterValueList;
    }

    /**
     * @param ValueListInterface $valueList
     * @return ValueListInterface
     */
    public function filterValues(ValueListInterface $valueList): ValueListInterface
    {
        $nextIndex = 0;
        $values = [];
        $innerMap = [];
        $filterOuterMap = $this->filterValueList->getOuterMap();
        foreach ($valueList->getValues() as $index => $value) {
            if (!\in_array($index, $filterOuterMap)) {
                continue;
            }
            $innerMap[$nextIndex] = $valueList->getOuterIndex($index);
            $values[$nextIndex++] = $value;
        }

        return new ValueList(\array_flip($innerMap), ...$values);
    }

    private function asBoolean($exportedValue): bool
    {
        if (is_int($exportedValue)) {
            return $exportedValue != 0;
        }

        if (is_bool($exportedValue)) {
            return $exportedValue;
        }

        return true;
    }
}
