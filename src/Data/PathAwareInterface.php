<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

interface PathAwareInterface
{

    public function getPath(): PathInterface;
}
