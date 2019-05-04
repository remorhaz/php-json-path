<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class ResultValueList implements ResultValueListInterface
{

    private $results;

    private $indexMap;

    private $values;

    public function __construct(array $indexMap, bool ...$results)
    {
        $this->results = $results;
        $this->indexMap = $indexMap;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getResult(int $index): bool
    {
        if (!isset($this->results[$index])) {
            throw new Exception\ResultNotFoundException($index);
        }

        return $this->results[$index];
    }

    public function getValues(): array
    {
        if (!isset($this->values)) {
            $this->values = array_map([$this, 'createResultValue'], $this->results);
        }

        return $this->values;
    }

    private function createResultValue(bool $result): ResultValueInterface
    {
        return new ResultValue($result);
    }

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
        return new self(array_keys($this->results), ...$this->results);
    }

    public function popIndexMap(ValueListInterface $mapSource): ValueListInterface
    {
        $indexMap = [];
        foreach (array_keys($this->indexMap) as $index) {
            $indexMap[] = $mapSource->getOuterIndex($index);
        }

        return new self($indexMap, ...$this->results);
    }
}
