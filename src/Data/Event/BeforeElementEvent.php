<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\Path\PathInterface;

final class BeforeElementEvent implements BeforeElementEventInterface
{

    private $index;

    private $path;

    public function __construct(int $index, PathInterface $path)
    {
        $this->index = $index;
        $this->path = $path;
    }

    public function getIndex(): int
    {
        return $this->index;
    }


    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
