<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;

interface NodeValueInterface
{

    public function getPath(): PathInterface;

    public function createIterator(): Iterator;
}