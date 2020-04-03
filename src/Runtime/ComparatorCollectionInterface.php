<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Comparator\ComparatorInterface;

interface ComparatorCollectionInterface
{

    public function equal(): ComparatorInterface;

    public function greater(): ComparatorInterface;
}
