<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Comparator\ComparatorInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\EvaluatedValueListBuilder;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\IndexMapInterface;
use Remorhaz\JSON\Path\Value\LiteralValueListInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Value\ValueListBuilder;
use Remorhaz\JSON\Path\Value\ValueListInterface;

use function array_fill;
use function count;
use function is_bool;
use function preg_match;

final class Evaluator implements EvaluatorInterface
{
    public function __construct(
        private ComparatorCollectionInterface $comparators,
        private Aggregator\AggregatorCollectionInterface $aggregators,
    ) {
    }

    public function logicalOr(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues,
    ): EvaluatedValueListInterface {
        $results = [];
        foreach ($leftValues->getResults() as $index => $leftResult) {
            $results[] = $leftResult || $rightValues->getResult($index);
        }

        return new EvaluatedValueList(
            $this->getEqualIndexMap($leftValues, $rightValues),
            ...$results,
        );
    }

    public function logicalAnd(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues,
    ): EvaluatedValueListInterface {
        $results = [];
        foreach ($leftValues->getResults() as $index => $leftResult) {
            $results[] = $leftResult && $rightValues->getResult($index);
        }

        return new EvaluatedValueList(
            $this->getEqualIndexMap($leftValues, $rightValues),
            ...$results,
        );
    }

    public function logicalNot(EvaluatedValueListInterface $values): EvaluatedValueListInterface
    {
        $results = [];
        foreach ($values->getResults() as $leftResult) {
            $results[] = !$leftResult;
        }

        return new EvaluatedValueList($values->getIndexMap(), ...$results);
    }

    public function isEqual(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues,
    ): EvaluatedValueListInterface {
        return $this->compare(
            $leftValues,
            $rightValues,
            $this->comparators->equal(),
        );
    }

    public function isGreater(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues,
    ): EvaluatedValueListInterface {
        return $this->compare(
            $leftValues,
            $rightValues,
            $this->comparators->greater(),
        );
    }

    private function compare(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues,
        ComparatorInterface $comparator
    ): EvaluatedValueListInterface {
        $valueListBuilder = new EvaluatedValueListBuilder();
        foreach ($leftValues->getIndexMap()->getOuterIndexes() as $leftInnerIndex => $leftOuterIndex) {
            foreach ($rightValues->getIndexMap()->getOuterIndexes() as $rightInnerIndex => $rightOuterIndex) {
                if (!isset($leftOuterIndex, $rightOuterIndex)) {
                    continue;
                }
                if ($leftOuterIndex != $rightOuterIndex) {
                    continue;
                }

                $valueListBuilder->addResult(
                    $comparator->compare(
                        $leftValues->getValue($leftInnerIndex),
                        $rightValues->getValue($rightInnerIndex),
                    ),
                    $leftOuterIndex
                );
            }
        }

        return $valueListBuilder->build();
    }

    public function isRegExp(string $regExp, ValueListInterface $values): EvaluatedValueListInterface
    {
        $results = [];

        foreach ($values->getValues() as $value) {
            if (!$value instanceof ScalarValueInterface) {
                $results[] = false;
                continue;
            }
            $data = $value->getData();
            if (!is_string($data)) {
                $results[] = false;
                continue;
            }
            $match = @preg_match($regExp, $data);
            if (false === $match) {
                throw new Exception\InvalidRegExpException($regExp);
            }
            $results[] = 1 === $match;
        }

        return new EvaluatedValueList($values->getIndexMap(), ...$results);
    }

    public function evaluate(
        ValueListInterface $sourceValues,
        ValueListInterface $resultValues,
    ): EvaluatedValueListInterface {
        if ($resultValues instanceof EvaluatedValueListInterface) {
            return $resultValues;
        }

        if ($resultValues instanceof LiteralValueListInterface) {
            return $this->evaluateLiteralValues($sourceValues, $resultValues);
        }

        $results = [];
        foreach ($sourceValues->getIndexMap()->getOuterIndexes() as $outerIndex) {
            $results[] = isset($outerIndex) && $resultValues->getIndexMap()->outerIndexExists($outerIndex);
        }

        return new EvaluatedValueList($sourceValues->getIndexMap(), ...$results);
    }

    private function evaluateLiteralValues(
        ValueListInterface $sourceValues,
        LiteralValueListInterface $resultValues,
    ): EvaluatedValueListInterface {
        $indexMap = $this->getEqualIndexMap($sourceValues, $resultValues);
        $literal = $resultValues->getLiteral();
        if ($literal instanceof ScalarValueInterface) {
            $data = $literal->getData();
            if (is_bool($data)) {
                return new EvaluatedValueList(
                    $indexMap,
                    ...array_fill(0, count($indexMap), $data),
                );
            }
        }

        throw new Exception\LiteralEvaluationFailedException($literal);
    }

    private function getEqualIndexMap(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues,
    ): IndexMapInterface {
        $indexMap = $leftValues->getIndexMap();

        return $indexMap->equals($rightValues->getIndexMap())
            ? $indexMap
            : throw new Exception\IndexMapMatchFailedException($leftValues, $rightValues);
    }

    public function aggregate(string $functionName, ValueListInterface $values): ValueListInterface
    {
        $aggregator = $this->aggregators->byName($functionName);
        $valuesBuilder = new ValueListBuilder();
        foreach ($values->getValues() as $innerIndex => $value) {
            $aggregatedValue = $aggregator->tryAggregate($value);
            if (isset($aggregatedValue)) {
                $valuesBuilder->addValue(
                    $aggregatedValue,
                    $values->getIndexMap()->getOuterIndex($innerIndex),
                );
            }
        }

        return $valuesBuilder->build();
    }
}
