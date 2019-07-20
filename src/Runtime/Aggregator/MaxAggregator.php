<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class MaxAggregator extends UniqueNumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $elementIndex = array_search(max(...$dataList), $dataList, true);
        if (false !== $elementIndex && isset($elements[$elementIndex])) {
            return $elements[$elementIndex];
        }

        throw new Exception\MaxElementNotFoundException($dataList, $elements);
    }
}
