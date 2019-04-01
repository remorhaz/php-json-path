<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\Event\ChildEventInterface;

final class AnyChildMatcher implements ChildMatcherInterface
{

    private $indices;

    public function __construct(int ...$indices)
    {
        $this->indices = $indices;
    }

    public function match(ChildEventInterface $event): bool
    {
        return true;
    }
}
