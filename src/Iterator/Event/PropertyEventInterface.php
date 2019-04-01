<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

interface PropertyEventInterface extends ChildEventInterface
{

    public function getName(): string;
}
