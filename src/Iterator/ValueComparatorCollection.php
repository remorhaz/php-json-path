<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class ValueComparatorCollection
{

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
    }

    public function equal(): ValueComparatorInterface
    {
        return new EqualValueComparator($this->valueIterator);
    }
}