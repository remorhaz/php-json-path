<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

interface ElementEventInterface extends ChildEventInterface
{

    public function getIndex(): int;
}
