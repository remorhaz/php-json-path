<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

use function array_search;
use function in_array;
use function is_int;

final class StrictElementMatcher implements SortedChildMatcherInterface
{

    private $indexes;

    public function __construct(int ...$indexes)
    {
        $this->indexes = $indexes;
    }

    public function match($address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return in_array($address, $this->indexes, true);
    }

    public function getSortIndex($address, NodeValueInterface $value, NodeValueInterface $container): int
    {
        $index = array_search($address, $this->indexes);

        if (is_int($index)) {
            return $index;
        }

        throw new Exception\AddressNotSortableException($address);
    }
}
