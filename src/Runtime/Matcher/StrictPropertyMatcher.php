<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class StrictPropertyMatcher implements SortedChildMatcherInterface
{

    private $properties;

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }

    public function match($address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return in_array($address, $this->properties, true);
    }

    public function getSortIndex($address, NodeValueInterface $value, NodeValueInterface $container): int
    {
        $index = array_search($address, $this->properties);

        if (is_int($index)) {
            return $index;
        }

        throw new Exception\AddressNotSortableException($address);
    }
}
