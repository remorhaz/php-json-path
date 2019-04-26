<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

use Iterator;

interface IteratorAwareEventInterface extends ValueEventInterface
{

    public function createIterator(): Iterator;
}
