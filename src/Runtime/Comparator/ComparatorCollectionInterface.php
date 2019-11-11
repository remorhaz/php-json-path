<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Comparator;

interface ComparatorCollectionInterface
{

    public function equal(): ComparatorInterface;

    public function greater(): ComparatorInterface;
}
