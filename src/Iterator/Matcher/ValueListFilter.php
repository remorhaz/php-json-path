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
        if (\array_keys($this->filterValueList->getValues()) !== \array_keys($valueList->getValues())) {
            //throw new Exception\InvalidValuesException();
        }

        $targetValues = [];
        $targetIndex = 0;
        $outerMap = $valueList->getOuterMap();
        $targetMap = [];
        foreach ($this->filterValueList->getValues() as $filterIndex => $filterValue) {
            $exportedValue = (new EventExporter(new Fetcher))->export($filterValue->createIterator());
            if (!$this->asBoolean($exportedValue)) {
                continue;
            }
            $targetValues[] = $valueList->getValues()[$filterIndex];
            $targetMap[$targetIndex++] = $outerMap[$filterIndex];
        }
        return new ValueList($targetMap, ...$targetValues);
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
