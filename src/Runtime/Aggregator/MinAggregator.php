<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_search;
use function min;

final class MinAggregator extends UniqueNumericAggregator
{
    /**
     * @param list<int|float>      $dataList
     * @param ScalarValueInterface ...$elements
     * @return ValueInterface|null
     */
    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $elementIndex = $this->findElementIndex($dataList);

        return isset($elementIndex, $elements[$elementIndex])
            ? $elements[$elementIndex]
            : throw new Exception\MaxElementNotFoundException($dataList, $elements);
    }

    private function findElementIndex(array $dataList): ?int
    {
        $elementIndex = array_search(min($dataList), $dataList, true);

        return false === $elementIndex ? null : $elementIndex;
    }
}
