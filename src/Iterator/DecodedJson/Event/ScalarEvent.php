<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;

final class ScalarEvent implements ScalarEventInterface
{

    private $data;

    private $path;

    private $iteratorFactory;

    public function __construct($data, PathInterface $path)
    {
        $this->iteratorFactory = new EventIteratorFactory($data, $path);
        $this->data = $data;
        $this->path = $path;
    }

    public function getPath(): PathInterface
    {
        return $this->iteratorFactory->getPath();
    }

    public function getData()
    {
        return $this->data;
    }

    public function createIterator(): Iterator
    {
        return $this->iteratorFactory->createIterator();
    }
}