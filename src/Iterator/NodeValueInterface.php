<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface NodeValueInterface extends PathAwareInterface, ValueInterface
{

    public function getPath(): PathInterface;
}