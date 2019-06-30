<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Comparator;

use Remorhaz\JSON\Path\Iterator\ValueInterface;

interface ValueComparatorInterface
{

    public function compare(ValueInterface $leftValue, ValueInterface $rightValue): bool;
}
