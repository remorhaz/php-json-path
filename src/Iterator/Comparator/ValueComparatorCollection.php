<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Comparator;

use Collator;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;

final class ValueComparatorCollection
{

    private $valueIteratorFactory;

    private $collator;

    public function __construct(ValueIteratorFactory $valueIteratorFactory, Collator $collator)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->collator = $collator;
    }

    public function equal(): ValueComparatorInterface
    {
        return new EqualValueComparator($this->valueIteratorFactory, $this->collator);
    }

    public function greater(): ValueComparatorInterface
    {
        return new GreaterValueComparator($this->valueIteratorFactory, $this->collator);
    }
}
