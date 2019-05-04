<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_fill;
use function count;

final class LiteralValueList implements LiteralValueListInterface
{

    private $indexMap;

    private $value;

    private $values;

    public function __construct(array $indexMap, LiteralValueInterface $value)
    {
        $this->indexMap = $indexMap;
        $this->value = $value;
    }

    public function getLiteral(): LiteralValueInterface
    {
        return $this->value;
    }

    public function getValues(): array
    {
        if (!isset($this->values)) {
            $this->values = array_fill(0, count($this->indexMap), $this->value);
        }

        return $this->values;
    }

    /**
     * @return int[]
     */
    public function getIndexMap(): array
    {
        return $this->indexMap;
    }

    public function getOuterIndex(int $valueIndex): int
    {
        if (!isset($this->indexMap[$valueIndex])) {
            throw new Exception\ValueOuterIndexNotFoundException($valueIndex);
        }

        return $this->indexMap[$valueIndex];
    }

    public function outerIndexExists(int $outerIndex): bool
    {
        return in_array($outerIndex, $this->indexMap, true);
    }

    public function pushIndexMap(): ValueListInterface
    {
        return new self(array_keys($this->indexMap), $this->value);
    }

    public function popIndexMap(ValueListInterface $mapSource): ValueListInterface
    {
        $indexMap = [];
        foreach (array_keys($this->indexMap) as $index) {
            $indexMap[] = $mapSource->getOuterIndex($index);
        }

        return new self($indexMap, $this->value);
    }
}
