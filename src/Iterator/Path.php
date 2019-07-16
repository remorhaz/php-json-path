<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class Path implements PathInterface
{

    private $elements;

    public function __construct(...$elements)
    {
        $this->elements = $elements;
    }

    public function copyWithElement(int $index): PathInterface
    {
        return new self(...$this->elements, ...[$index]);
    }

    public function copyWithProperty(string $name): PathInterface
    {
        return new self(...$this->elements, ...[$name]);
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function equals(PathInterface $path): bool
    {
        return $path->getElements() == $this->elements;
    }
}
