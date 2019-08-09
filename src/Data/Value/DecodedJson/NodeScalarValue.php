<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class NodeScalarValue implements NodeValueInterface, ScalarValueInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path)
    {
        if (null !== $data && !is_scalar($data)) {
            throw new Exception\InvalidNodeDataException($data, $path);
        }
        $this->data = $data;
        $this->path = $path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
