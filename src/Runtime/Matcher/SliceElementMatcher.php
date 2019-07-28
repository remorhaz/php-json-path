<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function is_int;
use function max;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class SliceElementMatcher implements ChildMatcherInterface
{

    private $count;

    private $start;

    private $end;

    private $step;

    private $isReverse;

    public function __construct(int $count, ?int $start, ?int $end, ?int $step)
    {
        $this->count = $count;
        $this->step = $step ?? 1;
        $this->isReverse = $step < 0;

        $this->start = $start;
        $this->end = $end;
    }

    public function match($address, ValueInterface $value): bool
    {
        if (0 == $this->step || !is_int($address) || 0 == $this->count) {
            return false;
        }
        $start = $this->detectStart($this->count);
        $end = $this->detectEnd($this->count);

        return $this->isInRange($address, $start, $end) && $this->isOnStep($address, $start);
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
        return 0 == ($this->isReverse ? $start - $address : $address - $start) % $this->step;
    }
}
