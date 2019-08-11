<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

interface ElementEventInterface extends EventInterface
{

    public function getIndex(): int;
}
