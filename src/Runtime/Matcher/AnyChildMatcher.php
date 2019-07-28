<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class AnyChildMatcher implements ChildMatcherInterface
{

    public function match($address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return true;
    }
}
