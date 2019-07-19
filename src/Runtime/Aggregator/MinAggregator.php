<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\ScalarValueInterface;
use Remorhaz\JSON\Data\ValueInterface;

final class MinAggregator extends UniqueNumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $elementIndex = array_search(min(...$dataList), $dataList, true);
        if (false !== $elementIndex && isset($elements[$elementIndex])) {
            return $elements[$elementIndex];
        }

        throw new Exception\AggregateFunctionFailedException('min');
    }
}
