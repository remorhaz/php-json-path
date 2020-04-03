<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

use function is_int;
use function iterator_count;
use function max;

final class SliceElementMatcher implements SortedChildMatcherInterface
{

    private $start;

    private $end;

    private $step;

    private $isReverse;

    public function __construct(?int $start, ?int $end, ?int $step)
    {
        $this->step = $step ?? 1;
        $this->isReverse = $this->step < 0;

        $this->start = $start;
        $this->end = $end;
    }

    public function match($address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        if (0 == $this->step || !is_int($address)) {
            return false;
        }

        $count = $this->findArrayLength($container);
        if (!isset($count)) {
            return false;
        }

        $start = $this->detectStart($count);
        $end = $this->detectEnd($count);

        return $this->isInRange($address, $start, $end) && $this->isOnStep($address, $start);
    }

    private function findArrayLength(NodeValueInterface $value): ?int
    {
        $count = $value instanceof ArrayValueInterface
            ? iterator_count($value->createChildIterator())
            : null;

        return isset($count) && $count > 0
            ? $count
            : null;
    }

    private function detectStart(int $count): int
    {
        $start = $this->start;
        if (!isset($start)) {
            $start = $this->isReverse ? -1 : 0;
        }
        if ($start < 0) {
            $start = max($start + $count, 0);
        }

        return $start;
    }

    private function detectEnd(int $count): int
    {
        $end = $this->end;
        if (!isset($end)) {
            $end = $this->isReverse ? -$count - 1 : $count;
        }
        if ($end > $count) {
            return $count;
        }

        return $end < 0
            ? max($end + $count, $this->isReverse ? -1 : 0)
            : $end;
    }

    private function isInRange(int $address, int $start, int $end): bool
    {
        return $this->isReverse
            ? $address <= $start && $address > $end
            : $address >= $start && $address < $end;
    }

    private function isOnStep(int $address, int $start): bool
    {
        return 0 == $this->getIndex($address, $start) % $this->step;
    }

    private function getIndex(int $address, int $start): int
    {
        return $this->isReverse ? $start - $address : $address - $start;
    }

    public function getSortIndex($address, NodeValueInterface $value, NodeValueInterface $container): int
    {
        $count = $this->findArrayLength($container);
        if (!isset($count)) {
            throw new Exception\AddressNotSortableException($address);
        }

        return $this->getIndex($address, $this->detectStart($count));
    }
}
