<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

final class MatcherFactory implements MatcherFactoryInterface
{
    public function matchAnyChild(): ChildMatcherInterface
    {
        return new AnyChildMatcher();
    }

    public function matchPropertyStrictly(string ...$nameList): ChildMatcherInterface
    {
        return new StrictPropertyMatcher(...$nameList);
    }

    public function matchElementStrictly(int ...$indexList): ChildMatcherInterface
    {
        return new StrictElementMatcher(...$indexList);
    }

    public function matchElementSlice(?int $start, ?int $end, ?int $step): ChildMatcherInterface
    {
        return new SliceElementMatcher($start, $end, $step);
    }
}
