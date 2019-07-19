<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

interface PathAwareInterface
{

    public function getPath(): PathInterface;
}
