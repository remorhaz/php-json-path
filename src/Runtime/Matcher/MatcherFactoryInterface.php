<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

interface MatcherFactoryInterface
{

    public function matchAnyChild(): ChildMatcherInterface;

    public function matchPropertyStrictly(string ...$nameList): ChildMatcherInterface;

    public function matchElementStrictly(int ...$indexList): ChildMatcherInterface;

    public function matchElementSlice(?int $start, ?int $end, ?int $step): ChildMatcherInterface;
}
