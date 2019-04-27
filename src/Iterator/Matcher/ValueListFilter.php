<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

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
        $outerMap = [];
        foreach ($valueList->getValues() as $index => $value) {
            if (!$this->filterValueList->outerIndexExists($index)) {
                continue;
            }
            $outerMap[$nextIndex] = $valueList->getOuterIndex($index);
            $values[$nextIndex++] = $value;
        }

        return new ValueList($outerMap, ...$values);
    }
}
