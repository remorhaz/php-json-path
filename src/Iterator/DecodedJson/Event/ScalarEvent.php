<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIterator;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;

final class ScalarEvent implements ScalarEventInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function getData()
    {
        return $this->data;
    }
}