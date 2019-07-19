<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\NodeValueListInterface;

interface ValueListFilterInterface
{

    public function filterValues(NodeValueListInterface $valueList): NodeValueListInterface;
}
