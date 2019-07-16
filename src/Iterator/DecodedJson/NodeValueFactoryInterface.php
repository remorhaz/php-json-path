<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;

interface NodeValueFactoryInterface
{

    public function createValue($data, PathInterface $path): NodeValueInterface;
}
