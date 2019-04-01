<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Iterator;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;

final class AfterObjectEvent implements AfterObjectEventInterface
{

    private $iterator;

    private $path;

    public function __construct(Iterator $iterator, PathInterface $path)
    {
        $this->iterator = $iterator;
        $this->path = $path;
    }

    /**
     * @return PathInterface
     */
    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function getIterator(): Iterator
    {
        return $this->iterator;
    }
}
