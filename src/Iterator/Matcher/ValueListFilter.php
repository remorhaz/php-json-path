<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\ResultValueInterface;
use Remorhaz\JSON\Path\Iterator\ResultValueListInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

class ValueListFilter implements ValueListFilterInterface
{

    private $filterValueList;

    public function __construct(ResultValueListInterface $filterValueList)
    {
        $this->filterValueList = $filterValueList;
    }

    /**
     * @param ValueListInterface $valueList
     * @return ValueListInterface
     */
    public function filterValues(ValueListInterface $valueList): ValueListInterface
    {
        if ($valueList->getIndexMap() !== $this->filterValueList->getIndexMap()) {
            throw new Exception\InvalidIndexMapException($valueList);
        }
        $nextIndex = 0;
        $values = [];
        $indexMap = [];
        $filterValues = $this->filterValueList->getValues();
        foreach ($valueList->getValues() as $index => $value) {
            $filterValue = $filterValues[$index];
            if (!$filterValue instanceof ResultValueInterface) {
                throw new Exception\InvalidResultException($filterValue);
            }
            if (!$filterValue->getData()) {
                continue;
            }
            $indexMap[$nextIndex] = $valueList->getOuterIndex($index);
            $values[$nextIndex++] = $value;
        }

        return new NodeValueList($indexMap, ...$values);
    }
}
