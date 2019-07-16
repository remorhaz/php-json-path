<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Iterator\IndexMap;
use Remorhaz\JSON\Path\Iterator\NodeValueListInterface;
use Remorhaz\JSON\Path\Iterator\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Iterator\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

class ValueListFilter implements ValueListFilterInterface
{

    private $filterValueList;

    public function __construct(EvaluatedValueListInterface $filterValueList)
    {
        $this->filterValueList = $filterValueList;
    }

    /**
     * @param ValueListInterface $valueList
     * @return ValueListInterface
     */
    public function filterValues(NodeValueListInterface $valueList): NodeValueListInterface
    {
        if (!$valueList->getIndexMap()->equals($this->filterValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($valueList);
        }
        $values = [];
        $indexMap = [];
        foreach ($valueList->getValues() as $index => $value) {
            $filterValue = $this->filterValueList->getValue($index);
            if (!$filterValue instanceof EvaluatedValueInterface) {
                throw new Exception\InvalidResultException($filterValue);
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
