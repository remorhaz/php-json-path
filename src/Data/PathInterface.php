<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

interface PathInterface
{

    public function copyWithElement(int $index): PathInterface;

    public function copyWithProperty(string $name): PathInterface;

    public function getElements(): array;

    public function equals(PathInterface $path): bool;
}
