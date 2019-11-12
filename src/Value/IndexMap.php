<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use function array_keys;
use function count;
use function in_array;

final class IndexMap implements IndexMapInterface
{

    private $outerIndexes;

    public function __construct(?int ...$outerIndexes)
    {
        $this->outerIndexes = $outerIndexes;
    }

    public function count()
    {
        return count($this->outerIndexes);
    }

    public function getInnerIndexes(): array
    {
        return array_keys($this->outerIndexes);
    }

    public function getOuterIndexes(): array
    {
        return $this->outerIndexes;
    }

    public function getOuterIndex(int $innerIndex): int
    {
        if (!isset($this->outerIndexes[$innerIndex])) {
            throw new Exception\OuterIndexNotFoundException($innerIndex, $this);
        }

        return $this->outerIndexes[$innerIndex];
    }

    public function outerIndexExists(int $outerIndex): bool
    {
        return in_array($outerIndex, $this->outerIndexes, true);
    }

    public function split(): IndexMapInterface
    {
        return new self(...$this->getInnerIndexes());
    }

    public function join(IndexMapInterface $indexMap): IndexMapInterface
    {
        $outerIndexes = [];
        foreach ($indexMap->getOuterIndexes() as $innerIndex => $outerIndex) {
            $outerIndexes[] = $this->outerIndexExists($innerIndex)
                ? $outerIndex
                : null;
        }

        return new self(...$outerIndexes);
    }

    public function equals(IndexMapInterface $indexMap): bool
    {
        return $this->outerIndexes === $indexMap->getOuterIndexes();
    }

    public function isCompatible(IndexMapInterface $indexMap): bool
    {
        if (count($indexMap) != count($this)) {
            return false;
        }

        $anotherMap = $indexMap->getOuterIndexes();
        foreach ($this->outerIndexes as $innerIndex => $outerIndex) {
            if (!isset($outerIndex, $anotherMap[$innerIndex])) {
                continue;
            }

            if ($outerIndex !== $anotherMap[$innerIndex]) {
                return false;
            }
        }

        return true;
    }
}
