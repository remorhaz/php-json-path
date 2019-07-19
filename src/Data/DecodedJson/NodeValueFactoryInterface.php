<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\DecodedJson;

use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\PathInterface;
use stdClass;

interface NodeValueFactoryInterface
{

    /**
     * Converts decoded JSON to JSON node value.
     *
     * @param array|bool|float|int|stdClass|string|null $data
     * @param PathInterface $path
     * @return NodeValueInterface
     */
    public function createValue($data, PathInterface $path): NodeValueInterface;
}
