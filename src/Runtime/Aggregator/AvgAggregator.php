<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use function array_sum;
use function count;
use Remorhaz\JSON\Data\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class AvgAggregator extends NumericAggregator
{

    protected function aggregateNumericData(array $dataList, ScalarValueInterface ...$elements): ?ValueInterface
    {
        return empty($dataList)
            ? null
            : new LiteralScalarValue(array_sum($dataList) / count($dataList));
    }
}
