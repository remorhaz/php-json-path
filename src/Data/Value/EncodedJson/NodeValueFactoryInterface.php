<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\EncodedJson;

use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface NodeValueFactoryInterface
{

    public function createValue(string $json, ?PathInterface $path = null): NodeValueInterface;
}
