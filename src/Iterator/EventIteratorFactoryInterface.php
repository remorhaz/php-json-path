<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;

interface EventIteratorFactoryInterface
{

    public function createIterator(): Iterator;

    public function getPath(): PathInterface;
}
