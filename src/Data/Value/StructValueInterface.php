<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use Iterator;

interface StructValueInterface extends ValueInterface
{

    public function createChildIterator(): Iterator;
}
