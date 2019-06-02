<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ValueAggregatorInterface
{

    public function tryAggregate(ValueInterface $value): ?ValueInterface;
}
