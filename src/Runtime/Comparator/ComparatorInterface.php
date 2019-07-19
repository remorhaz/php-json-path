<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Comparator;

use Remorhaz\JSON\Data\ValueInterface;

interface ComparatorInterface
{

    public function compare(ValueInterface $leftValue, ValueInterface $rightValue): bool;
}
