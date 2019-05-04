<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\NodeValueListInterface;

interface ValueListFilterInterface
{

    public function filterValues(NodeValueListInterface $valueList): NodeValueListInterface;
}
