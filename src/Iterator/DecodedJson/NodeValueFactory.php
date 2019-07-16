<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use function is_array;
use function is_scalar;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use stdClass;

final class NodeValueFactory implements NodeValueFactoryInterface
{

    public function createValue($data, PathInterface $path): NodeValueInterface
    {
        if (null === $data || is_scalar($data)) {
            return new NodeScalarValue($data, $path);
        }

        if (is_array($data)) {
            return new NodeArrayValue($data, $path, $this);
        }

        if ($data instanceof stdClass) {
            return new NodeObjectValue($data, $path, $this);
        }

        throw new Exception\InvalidNodeDataException($data, $path);
    }
}
