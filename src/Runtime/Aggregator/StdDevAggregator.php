<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use function array_map;
use function array_sum;
use function count;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use function sqrt;

final class StdDevAggregator extends NumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        $count = count($dataList);
        if ($count < 2) {
            return null;
        }

        $meanValue = array_sum($dataList) / $count;
        $calculateSquaredDifferenceFromMean = function ($value) use ($meanValue): float {
            return ($value - $meanValue) ** 2;
        };

        $squaredDifferencesSum = array_sum(array_map($calculateSquaredDifferenceFromMean, $dataList));
        $variance = $squaredDifferencesSum / ($count - 1);
        $standardDeviation = sqrt($variance);

        return new LiteralScalarValue($standardDeviation);
    }
}
