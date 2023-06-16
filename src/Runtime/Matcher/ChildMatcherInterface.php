<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface ChildMatcherInterface
{
    public function match(int|string $address, NodeValueInterface $value, NodeValueInterface $container): bool;
}
