<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ValueListInterface
{

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array;

    /**
     * @return int[]
     */
    public function getOuterMap(): array;

    public function getOuterIndex(int $valueIndex): int;
}
