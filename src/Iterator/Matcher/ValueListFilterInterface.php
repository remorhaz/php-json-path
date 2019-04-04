<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\ValueInterface;

interface ValueListFilterInterface
{

    public function filterValues(ValueInterface ...$values): array;
}
