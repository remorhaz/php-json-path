<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ValueListInterface
{

    public function getValue(int $index): ValueInterface;

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array;

    public function getIndexMap(): IndexMapInterface;
}
