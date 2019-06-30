<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Comparator;

use Collator;
use Remorhaz\JSON\Path\Iterator\ValueIterator;

final class ValueComparatorCollection
{

    private $valueIterator;

    private $collator;

    public function __construct(ValueIterator $valueIterator, Collator $collator)
    {
        $this->valueIterator = $valueIterator;
        $this->collator = $collator;
    }

    public function equal(): ValueComparatorInterface
    {
        return new EqualValueComparator($this->valueIterator, $this->collator);
    }

    public function greater(): ValueComparatorInterface
    {
        return new GreaterValueComparator($this->valueIterator, $this->collator);
    }
}
