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
    public function getIndexMap(): array;

    public function getOuterIndex(int $valueIndex): int;

    public function outerIndexExists(int $outerIndex): bool;

    public function pushIndexMap(): ValueListInterface;

    public function popIndexMap(ValueListInterface $mapSource): ValueListInterface;

    public function withLiteral(LiteralValueInterface $value): ValueListInterface;

    public function containsNodes(): bool;

    public function containsResults(): bool;

    public function containsLiterals(): bool;
}
