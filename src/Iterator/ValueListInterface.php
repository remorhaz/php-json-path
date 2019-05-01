<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ValueListInterface
{

    /**
     * @return NodeValueInterface[]
     */
    public function getValues(): array;

    /**
     * @return int[]
     */
    public function getOuterMap(): array;

    public function getOuterIndex(int $valueIndex): int;

    public function outerIndexExists(int $outerIndex): bool;
}
