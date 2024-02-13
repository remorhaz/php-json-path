<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

use function array_search;
use function in_array;
use function is_int;

final class StrictElementMatcher implements SortedChildMatcherInterface
{
    private array $indexes;

    public function __construct(int ...$indexes)
    {
        $this->indexes = $indexes;
    }

    public function match(int|string $address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return in_array($address, $this->indexes, true);
    }

    public function getSortIndex(int|string $address, NodeValueInterface $value, NodeValueInterface $container): int
    {
        $index = array_search($address, $this->indexes);

        return is_int($index)
            ? $index
            : throw new Exception\AddressNotSortableException($address);
    }
}
