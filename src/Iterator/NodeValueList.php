<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_keys;
use function in_array;

final class NodeValueList implements NodeValueListInterface
{

    private $values;

    private $indexMap;

    public static function createRootNodes(NodeValueInterface ...$values): NodeValueListInterface
    {
        return new self(array_keys($values), ...$values);
    }

    public function __construct(array $indexMap, ValueInterface ...$values)
    {
        $this->values = $values;
        $this->indexMap = $indexMap;
    }

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array
    {
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
        return new self(array_keys($this->values), ...$this->values);
    }

    public function popIndexMap(ValueListInterface $mapSource): ValueListInterface
    {
        $indexMap = [];
        foreach (array_keys($this->indexMap) as $index) {
            $indexMap[] = $mapSource->getOuterIndex($index);
        }

        return new self($indexMap, ...$this->values);
    }
}
