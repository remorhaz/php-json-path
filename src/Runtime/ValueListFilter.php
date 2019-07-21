<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Value\IndexMap;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\NodeValueList;

class ValueListFilter implements ValueListFilterInterface
{

    private $filterValueList;

    public function __construct(EvaluatedValueListInterface $filterValueList)
    {
        $this->filterValueList = $filterValueList;
    }

    /**
     * @param NodeValueListInterface $valueList
     * @return NodeValueListInterface
     */
    public function filterValues(NodeValueListInterface $valueList): NodeValueListInterface
    {
        if (!$valueList->getIndexMap()->equals($this->filterValueList->getIndexMap())) {
            throw new Exception\IndexMapMatchFailedException($valueList, $this->filterValueList);
        }
        $values = [];
        $indexMap = [];
        foreach ($valueList->getValues() as $index => $value) {
            $filterValue = $this->filterValueList->getValue($index);
            if (!$filterValue instanceof EvaluatedValueInterface) {
                throw new Exception\InvalidFilterValueException($filterValue);
            }
            if (!$filterValue->getData()) {
                continue;
            }
            $indexMap[] = $valueList->getIndexMap()->getOuterIndex($index);
            $values[] = $value;
        }

        return new NodeValueList(new IndexMap(...$indexMap), ...$values);
    }
}
