<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

interface ValueListInterface
{

    public function getValue(int $index): ValueInterface;

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array;

    public function getIndexMap(): IndexMapInterface;
}
