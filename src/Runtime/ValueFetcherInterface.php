<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Iterator;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueFetcherInterface
{
    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface            $value
     * @return Iterator<ValueInterface>
     */
    public function createChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value,
    ): Iterator;

    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface            $value
     * @return Iterator<ValueInterface>
     */
    public function createDeepChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value,
    ): Iterator;
}
