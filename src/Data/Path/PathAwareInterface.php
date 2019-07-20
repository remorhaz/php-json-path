<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Path;

interface PathAwareInterface
{

    public function getPath(): PathInterface;
}
