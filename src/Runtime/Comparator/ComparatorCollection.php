<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Comparator;

use Collator;

final class ComparatorCollection implements ComparatorCollectionInterface
{

    private $collator;

    public function __construct(Collator $collator)
    {
        $this->collator = $collator;
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
