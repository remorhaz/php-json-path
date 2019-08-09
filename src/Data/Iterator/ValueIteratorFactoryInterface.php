<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator;

use Iterator;

interface ValueIteratorFactoryInterface
{
    public function createArrayIterator(Iterator $eventIterator): Iterator;

    public function createObjectIterator(Iterator $eventIterator): Iterator;
}
