<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ResultValueListInterface extends ValueListInterface
{
    /**
     * @return bool[]
     */
    public function getResults(): array;

    public function getResult(int $index): bool;
}
