<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\PathInterface;

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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
