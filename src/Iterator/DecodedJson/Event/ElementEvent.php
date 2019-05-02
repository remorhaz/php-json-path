<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;

final class ElementEvent implements ElementEventInterface
{

    private $index;

    private $path;

    public function __construct(int $index, PathInterface $path)
    {
        $this->index = $index;
        $this->path = $path;
    }

    /**
     * @return PathInterface
     */
    public function getPath(): PathInterface
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }
}
