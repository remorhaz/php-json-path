<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

use Remorhaz\JSON\Path\Iterator\PathInterface;

interface DataEventInterface
{

    public function getPath(): PathInterface;
}
