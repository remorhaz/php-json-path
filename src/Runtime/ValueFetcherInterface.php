<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface ValueFetcherInterface
{

    public function fetchValueChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array;

    public function fetchValueDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array;

    public function fetchArrayLength(NodeValueInterface $value): ?int;
}
