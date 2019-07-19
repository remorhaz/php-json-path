<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\ValueInterface;

interface ValueAggregatorInterface
{

    public function tryAggregate(ValueInterface $value): ?ValueInterface;
}
