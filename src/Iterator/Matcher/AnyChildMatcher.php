<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

final class AnyChildMatcher implements ChildMatcherInterface
{

    public function match($address): bool
    {
        return true;
    }
}
