<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_sum;
use function count;

final class AvgAggregator extends NumericAggregator
{
    /**
     * @param list<int|float>      $dataList
     * @param ScalarValueInterface ...$elements
     * @return ValueInterface|null
     */
    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        return empty($dataList)
            ? null
            : new LiteralScalarValue(array_sum($dataList) / count($dataList));
    }
}
