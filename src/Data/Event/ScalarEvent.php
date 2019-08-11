<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\Path\PathInterface;

final class ScalarEvent implements ScalarEventInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path)
    {
        if (null !== $data && !is_scalar($data)) {
            throw new Exception\InvalidScalarDataException($data);
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
