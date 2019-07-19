<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use function array_fill;
use function count;
use function is_bool;
use function preg_match;
use Remorhaz\JSON\Data\EvaluatedValueList;
use Remorhaz\JSON\Data\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\IndexMap;
use Remorhaz\JSON\Data\LiteralValueListInterface;
use Remorhaz\JSON\Data\NodeValueList;
use Remorhaz\JSON\Data\ScalarValueInterface;
use Remorhaz\JSON\Data\ValueListInterface;

final class Evaluator
{

    private $comparators;

    private $aggregators;

    public function __construct(
        Comparator\ComparatorCollection $comparators,
        Aggregator\AggregatorCollection $aggregators
    ) {
        $this->comparators = $comparators;
        $this->aggregators = $aggregators;
    }

    public function logicalOr(
        EvaluatedValueListInterface $leftValueList,
        EvaluatedValueListInterface $rightValueList
    ): EvaluatedValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }

        $results = [];
        foreach ($leftValueList->getResults() as $index => $leftResult) {
            $results[] = $leftResult || $rightValueList->getResult($index);
        }

        return new EvaluatedValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function logicalAnd(
        EvaluatedValueListInterface $leftValueList,
        EvaluatedValueListInterface $rightValueList
    ): EvaluatedValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }

        $results = [];
        foreach ($leftValueList->getResults() as $index => $leftResult) {
            $results[] = $leftResult && $rightValueList->getResult($index);
        }

        return new EvaluatedValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function logicalNot(EvaluatedValueListInterface $valueList): EvaluatedValueListInterface
    {
        $results = [];
        foreach ($valueList->getResults() as $leftResult) {
            $results[] = !$leftResult;
        }

        return new EvaluatedValueList($valueList->getIndexMap(), ...$results);
    }

    public function isEqual(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): EvaluatedValueListInterface {
        return $this->compare(
            $leftValueList,
            $rightValueList,
            $this->comparators->equal()
        );
    }

    public function isGreater(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): EvaluatedValueListInterface {
        return $this->compare(
            $leftValueList,
            $rightValueList,
            $this->comparators->greater()
        );
    }

    private function compare(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList,
        Comparator\ComparatorInterface $comparator
    ): EvaluatedValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }
        $results = [];

        foreach ($leftValueList->getValues() as $index => $leftValue) {
            $results[] = $comparator
                ->compare($leftValue, $rightValueList->getValue($index));
        }
        return new EvaluatedValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function isRegExp(ValueListInterface $valueList, string $regexp): EvaluatedValueListInterface
    {
        $results = [];

        foreach ($valueList->getValues() as $value) {
            if (!$value instanceof ScalarValueInterface) {
                $results[] = false;
                continue;
            }
            $data = $value->getData();
            if (!is_string($data)) {
                $results[] = false;
                continue;
            }
            $match = @preg_match($regexp, $data);
            if (false === $match) {
                throw new Exception\InvalidRegExpException($regexp);
            }
            $results[] = 1 === $match;
        }

        return new EvaluatedValueList($valueList->getIndexMap(), ...$results);
    }

    public function evaluate(
        ValueListInterface $sourceValues,
        ValueListInterface $resultValues
    ): EvaluatedValueListInterface {
        if ($resultValues instanceof EvaluatedValueListInterface) {
            return $resultValues;
        }

        if ($resultValues instanceof LiteralValueListInterface) {
            $literal = $resultValues->getLiteral();
            if ($literal instanceof ScalarValueInterface) {
                $data = $literal->getData();
                if (is_bool($data)) {
                    return new EvaluatedValueList(
                        $resultValues->getIndexMap(),
                        ...array_fill(0, count($resultValues->getIndexMap()), $data)
                    );
                }
            }

            throw new Exception\LiteralEvaluatonErrorException($literal);
        }

        $results = [];
        foreach ($sourceValues->getIndexMap()->toArray() as $outerIndex) {
            $results[] = $resultValues->getIndexMap()->outerIndexExists($outerIndex);
        }

        return new EvaluatedValueList($sourceValues->getIndexMap(), ...$results);
    }

    public function aggregate(string $functionName, ValueListInterface $valueList): ValueListInterface
    {
        $aggregator = $this->aggregators->byName($functionName);
        $results = [];
        $indexMap = [];
        foreach ($valueList->getValues() as $innerIndex => $value) {
            $minValue = $aggregator->tryAggregate($value);
            if (isset($minValue)) {
                $results[] = $minValue;
                $indexMap[] = $valueList->getIndexMap()->getOuterIndex($innerIndex);
            }
        }

        return new NodeValueList(new IndexMap(...$indexMap), ...$results);
    }
}
