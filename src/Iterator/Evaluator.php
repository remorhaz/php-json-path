<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_fill;
use function count;
use function is_bool;
use function preg_match;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;

final class Evaluator
{

    private $comparators;

    public function __construct(ValueComparatorCollection $comparators)
    {
        $this->comparators = $comparators;
    }

    public function logicalOr(
        ResultValueListInterface $leftValueList,
        ResultValueListInterface $rightValueList
    ): ResultValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }

        $results = [];
        foreach ($leftValueList->getResults() as $index => $leftResult) {
            $results[] = $leftResult || $rightValueList->getResult($index);
        }

        return new ResultValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function logicalAnd(
        ResultValueListInterface $leftValueList,
        ResultValueListInterface $rightValueList
    ): ResultValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }

        $results = [];
        foreach ($leftValueList->getResults() as $index => $leftResult) {
            $results[] = $leftResult && $rightValueList->getResult($index);
        }

        return new ResultValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function logicalNot(ResultValueListInterface $valueList): ResultValueListInterface
    {
        $results = [];
        foreach ($valueList->getResults() as $leftResult) {
            $results[] = !$leftResult;
        }

        return new ResultValueList($valueList->getIndexMap(), ...$results);
    }

    public function isEqual(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): ResultValueListInterface {
        return $this->compare(
            $leftValueList,
            $rightValueList,
            $this->comparators->equal()
        );
    }

    public function isGreater(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): ResultValueListInterface {
        return $this->compare(
            $leftValueList,
            $rightValueList,
            $this->comparators->greater()
        );
    }

    private function compare(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList,
        ValueComparatorInterface $comparator
    ): ResultValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }
        $results = [];

        foreach ($leftValueList->getValues() as $index => $leftValue) {
            $results[] = $comparator
                ->compare($leftValue, $rightValueList->getValue($index));
        }
        return new ResultValueList($leftValueList->getIndexMap(), ...$results);
    }

    public function isRegExp(ValueListInterface $valueList, string $regexp): ResultValueListInterface
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

        return new ResultValueList($valueList->getIndexMap(), ...$results);
    }

    public function evaluate(
        ValueListInterface $sourceValues,
        ValueListInterface $resultValues
    ): ResultValueListInterface {
        if ($resultValues instanceof ResultValueListInterface) {
            return $resultValues;
        }

        if ($resultValues instanceof LiteralValueListInterface) {
            $literal = $resultValues->getLiteral();
            if ($literal instanceof ScalarValueInterface) {
                $data = $literal->getData();
                if (is_bool($data)) {
                    return new ResultValueList(
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

        return new ResultValueList($sourceValues->getIndexMap(), ...$results);
    }
}
