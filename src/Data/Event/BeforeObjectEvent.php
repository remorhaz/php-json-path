<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\Path\PathInterface;

final class BeforeObjectEvent implements BeforeObjectEventInterface
{

    private $path;

    public function __construct(PathInterface $path)
    {
        $this->path = $path;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
