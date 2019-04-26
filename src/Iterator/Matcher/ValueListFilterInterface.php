<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\ValueListInterface;

interface ValueListFilterInterface
{

    public function filterValues(ValueListInterface $valueList): ValueListInterface;
}
