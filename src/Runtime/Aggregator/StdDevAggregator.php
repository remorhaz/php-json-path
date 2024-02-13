<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_map;
use function array_sum;
use function count;
use function sqrt;

final class StdDevAggregator extends NumericAggregator
{
    /**
     * @param list<int|float|string|bool|null> $dataList
     * @param ScalarValueInterface ...$elements
     * @return ValueInterface|null
     */
    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $count = count($dataList);
        if ($count < 2) {
            return null;
        }

        $meanValue = array_sum($dataList) / $count;
        $calculateSquaredDifferenceFromMean = static fn ($value): float => ($value - $meanValue) ** 2;

        $squaredDifferencesSum = array_sum(array_map($calculateSquaredDifferenceFromMean, $dataList));
        $variance = $squaredDifferencesSum / ($count - 1);
        $standardDeviation = sqrt($variance);

        return new LiteralScalarValue($standardDeviation);
    }
}
