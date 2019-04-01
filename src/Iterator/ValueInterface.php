<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;

interface ValueInterface
{

    public function getPath(): PathInterface;

    public function getIterator(): Iterator;
}