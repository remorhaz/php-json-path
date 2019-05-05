<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_fill;
use function count;
use function is_bool;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;

final class Evaluator
{

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

    public function isEqual(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): ResultValueListInterface {
        if (!$leftValueList->getIndexMap()->equals($rightValueList->getIndexMap())) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }
        $results = [];

        foreach ($leftValueList->getValues() as $index => $leftValue) {
            $results[] = $this->isEqualValue($leftValue, $rightValueList->getValue($index));
        }
        return new ResultValueList($leftValueList->getIndexMap(), ...$results);
    }

    private function isEqualValue(ValueInterface $leftValue, ValueInterface $rightValue): bool
    {
        if ($leftValue instanceof ScalarValueInterface && $rightValue instanceof ScalarValueInterface) {
            return $leftValue->getData() === $rightValue->getData();
        }

        return false;
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
