<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueIteratorFactoryInterface
{
    public function createArrayIterator(Iterator $eventIterator): Generator;

    public function createObjectIterator(Iterator $eventIterator): Generator;

    /**
     * @param Iterator $eventIterator
     * @return ValueInterface
     * @todo Move this method to fetcher?
     */
    public function fetchValue(Iterator $eventIterator): ValueInterface;
}
