<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Iterator;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface ValueFetcherInterface
{

    public function createChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator;

    public function createDeepChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator;
}
