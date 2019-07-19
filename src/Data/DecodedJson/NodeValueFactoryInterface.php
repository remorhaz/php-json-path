<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\DecodedJson;

use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\PathInterface;

interface NodeValueFactoryInterface
{

    public function createValue($data, PathInterface $path): NodeValueInterface;
}
