<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface SortedChildMatcherInterface extends ChildMatcherInterface
{
    public function getSortIndex(int|string $address, NodeValueInterface $value, NodeValueInterface $container): int;
}
