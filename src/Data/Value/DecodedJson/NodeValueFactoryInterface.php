<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use stdClass;

interface NodeValueFactoryInterface
{

    /**
     * Converts decoded JSON to JSON node value.
     *
     * @param array|bool|float|int|stdClass|string|null $data
     * @param PathInterface|null $path
     * @return NodeValueInterface
     */
    public function createValue($data, ?PathInterface $path = null): NodeValueInterface;
}
