<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Aggregator;

use Remorhaz\JSON\Path\Iterator\ValueInterface;

interface ValueAggregatorInterface
{

    public function tryAggregate(ValueInterface $value): ?ValueInterface;
}
