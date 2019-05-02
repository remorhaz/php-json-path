<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface PathAwareInterface
{

    public function getPath(): PathInterface;
}
