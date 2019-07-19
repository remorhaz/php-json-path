<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use Iterator;

interface ValueInterface
{

    public function createIterator(): Iterator;
}
