<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use Remorhaz\JSON\Data\Exception;

final class EvaluatedValueList implements EvaluatedValueListInterface
{

    private $results;

    private $indexMap;

    private $values;

    public function __construct(IndexMapInterface $indexMap, bool ...$results)
    {
        $this->results = $results;
        $this->indexMap = $indexMap;
    }

    public function getValue(int $index): ValueInterface
    {
        $values = $this->getValues();
        if (!isset($values[$index])) {
            throw new Exception\ValueNotFoundException($index);
        }

        return $values[$index];
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
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

    private function createResultValue(bool $result): EvaluatedValueInterface
    {
        return new EvaluatedValue($result);
    }
}
