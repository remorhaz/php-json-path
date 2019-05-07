<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ValueComparatorInterface
{

    public function compare(ValueInterface $leftValue, ValueInterface $rightValue): bool;
}
