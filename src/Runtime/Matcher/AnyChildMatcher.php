<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\ValueInterface;

final class AnyChildMatcher implements ChildMatcherInterface
{

    public function match($address, ValueInterface $value): bool
    {
        return true;
    }
}
