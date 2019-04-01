<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use function in_array;
use Remorhaz\JSON\Path\Iterator\Event\ChildEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;

final class StrictPropertyMatcher implements ChildMatcherInterface
{

    private $properties;

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }

    public function match(ChildEventInterface $event): bool
    {
        return $event instanceof PropertyEventInterface
            ? in_array($event->getName(), $this->properties, true)
            : false;
    }
}
