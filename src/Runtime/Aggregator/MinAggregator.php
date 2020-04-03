<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_search;
use function min;

final class MinAggregator extends UniqueNumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $elementIndex = $this->findElementIndex($dataList);
        if (isset($elementIndex, $elements[$elementIndex])) {
            return $elements[$elementIndex];
        }

        // @codeCoverageIgnoreStart
        throw new Exception\MaxElementNotFoundException($dataList, $elements);
        // @codeCoverageIgnoreEnd
    }

    private function findElementIndex(array $dataList): ?int
    {
        $elementIndex = array_search(min($dataList), $dataList, true);

        return false === $elementIndex ? null : $elementIndex;
    }
}
