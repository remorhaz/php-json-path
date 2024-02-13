<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Collator;
use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Data\Comparator\EqualValueComparator;
use Remorhaz\JSON\Data\Comparator\GreaterValueComparator;

final class ComparatorCollection implements ComparatorCollectionInterface
{
    public function __construct(
        private readonly Collator $collator,
    ) {
    }

    public function equal(): ComparatorInterface
    {
        return new EqualValueComparator($this->collator);
    }

    public function greater(): ComparatorInterface
    {
        return new GreaterValueComparator($this->collator);
    }
}
