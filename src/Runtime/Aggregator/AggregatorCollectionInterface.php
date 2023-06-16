<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

interface AggregatorCollectionInterface
{
    public function byName(string $name): ValueAggregatorInterface;
}
