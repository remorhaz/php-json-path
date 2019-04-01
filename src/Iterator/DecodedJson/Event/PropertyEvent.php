<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;

final class PropertyEvent implements PropertyEventInterface
{

    private $name;

    private $path;

    public function __construct(string $name, PathInterface $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @return PathInterface
     */
    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function getChildPath(): PathInterface
    {
        return $this->path->copyWithProperty($this->name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
