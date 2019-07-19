<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Comparator;

use Collator;
use Remorhaz\JSON\Data\Value\ValueIteratorFactory;

final class ComparatorCollection
{

    private $valueIteratorFactory;

    private $collator;

    public function __construct(ValueIteratorFactory $valueIteratorFactory, Collator $collator)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->collator = $collator;
    }

    public function equal(): ComparatorInterface
    {
        return new EqualValueComparator($this->valueIteratorFactory, $this->collator);
    }

    public function greater(): ComparatorInterface
    {
        return new GreaterValueComparator($this->valueIteratorFactory, $this->collator);
    }
}
