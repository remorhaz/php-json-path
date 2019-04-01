<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use function in_array;
use Remorhaz\JSON\Path\Iterator\Event\ChildEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indices;

    public function __construct(int ...$indices)
    {
        $this->indices = $indices;
    }

    public function match(ChildEventInterface $event): bool
    {
        return $event instanceof ElementEventInterface
            ? in_array($event->getIndex(), $this->indices, true)
            : false;
    }
}
