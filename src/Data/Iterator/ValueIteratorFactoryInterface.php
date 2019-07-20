<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueIteratorFactoryInterface
{
    public function createArrayIterator(Iterator $iterator): Generator;

    public function createObjectIterator(Iterator $iterator): Generator;

    /**
     * @param Iterator $iterator
     * @return ValueInterface
     * @todo Move this method to fetcher?
     */
    public function fetchValue(Iterator $iterator): ValueInterface;
}
