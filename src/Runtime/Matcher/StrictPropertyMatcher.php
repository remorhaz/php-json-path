<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

use function in_array;

final class StrictPropertyMatcher implements SortedChildMatcherInterface
{
    private array $properties;

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }

    public function match(int|string $address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return in_array($address, $this->properties, true);
    }

    public function getSortIndex($address, NodeValueInterface $value, NodeValueInterface $container): int
    {
        $index = array_search($address, $this->properties);

        return is_int($index)
            ? $index
            : throw new Exception\AddressNotSortableException($address);
    }
}
