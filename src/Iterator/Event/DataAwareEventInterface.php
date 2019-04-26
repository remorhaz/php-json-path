<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

interface DataAwareEventInterface extends ValueEventInterface
{

    public function getData();
}
