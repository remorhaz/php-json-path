<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator;

use Iterator;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueIteratorFactoryInterface
{
    public function createArrayIterator(Iterator $eventIterator): Iterator;

    public function createObjectIterator(Iterator $eventIterator): Iterator;

    /**
     * @param Iterator $eventIterator
     * @return ValueInterface
     * @todo Move this method to fetcher?
     */
    public function fetchValue(Iterator $eventIterator): ValueInterface;
}
