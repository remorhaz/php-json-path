<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use function array_values;

final class EvaluatedValueList implements EvaluatedValueListInterface
{
    /**
     * @var list<bool>
     */
    private array $results;

    /**
     * @var list<EvaluatedValueInterface>
     */
    private array $values;

    public function __construct(
        private readonly IndexMapInterface $indexMap,
        bool ...$results,
    ) {
        $this->results = array_values($results);
    }

    public function getValue(int $index): EvaluatedValueInterface
    {
        return $this->getValues()[$index] ?? throw new Exception\ValueNotFoundException($index, $this);
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }

    /**
     * @return list<bool>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getResult(int $index): bool
    {
        return $this->results[$index] ??
            throw new Exception\ResultNotFoundException($index, $this);
    }

    /**
     * @return list<EvaluatedValueInterface>
     */
    public function getValues(): array
    {
        return $this->values ??= array_map($this->createResultValue(...), $this->results);
    }

    private function createResultValue(bool $result): EvaluatedValueInterface
    {
        return new EvaluatedValue($result);
    }
}
