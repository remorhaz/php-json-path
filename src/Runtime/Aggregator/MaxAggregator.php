<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Path\Iterator\ScalarValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class MaxAggregator extends UniqueNumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $elementIndex = array_search(max(...$dataList), $dataList, true);
        if (false !== $elementIndex && isset($elements[$elementIndex])) {
            return $elements[$elementIndex];
        }

        throw new Exception\AggregateFunctionFailedException('max');
    }
}
