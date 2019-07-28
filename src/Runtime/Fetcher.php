<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use function array_push;
use Iterator;
use function max;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class Fetcher
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    /**
     * @param NodeValueListInterface $source
     * @param Matcher\ChildMatcherInterface ...$matcherList
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface ...$matcherList
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $matcher = $matcherList[$sourceIndex];
            $children = $this->fetchValueChildren($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueListInterface $source
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchValueDeepChildren($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface $value
     * @return NodeValueInterface[]
     */
    private function fetchValueChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array {
        if ($value instanceof ScalarValueInterface) {
            return [];
        }

        if ($value instanceof ArrayValueInterface) {
            return $this->fetchElements($value->createIterator(), $matcher);
        }

        if ($value instanceof ObjectValueInterface) {
            return $this->fetchProperties($value->createIterator(), $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function fetchValueDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array {
        if ($value instanceof ScalarValueInterface) {
            return [];
        }

        if ($value instanceof ArrayValueInterface) {
            return $this->fetchDeepElements($value->createIterator(), $matcher);
        }

        if ($value instanceof ObjectValueInterface) {
            return $this->fetchDeepProperties($value->createIterator(), $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function fetchDeepElements(Iterator $iterator, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createArrayIterator($iterator) as $index => $element) {
            if ($matcher->match($index)) {
                $results[] = $element;
            }
            array_push(
                $results,
                ...$this->fetchValueDeepChildren($matcher, $element)
            );
        }

        return $results;
    }

    private function fetchDeepProperties(Iterator $iterator, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createObjectIterator($iterator) as $name => $property) {
            if ($matcher->match($name)) {
                $results[] = $property;
            }
            array_push(
                $results,
                ...$this->fetchValueDeepChildren($matcher, $property)
            );
        }

        return $results;
    }

    public function fetchFilterContext(NodeValueListInterface $source): NodeValueListInterface
    {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            if (!$sourceValue instanceof NodeValueInterface) {
                throw new Exception\InvalidContextValueException($sourceValue);
            }
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            $children = $sourceValue instanceof ArrayValueInterface
                ? $this->fetchValueChildren(new Matcher\AnyChildMatcher, $sourceValue)
                : [$sourceValue];
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchFilteredValues(
        EvaluatedValueListInterface $results,
        NodeValueListInterface $values
    ): NodeValueListInterface {
        if (!$values->getIndexMap()->equals($results->getIndexMap())) {
            throw new Exception\IndexMapMatchFailedException($values, $results);
        }
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($values->getValues() as $index => $value) {
            $evaluatedValue = $results->getValue($index);
            if (!$evaluatedValue instanceof EvaluatedValueInterface) {
                throw new Exception\InvalidFilterValueException($evaluatedValue);
            }
            if (!$evaluatedValue->getData()) {
                continue;
            }
            $nodesBuilder->addValue(
                $value,
                $values->getIndexMap()->getOuterIndex($index)
            );
        }

        return $nodesBuilder->build();
    }

    private function fetchElements(Iterator $iterator, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createArrayIterator($iterator) as $index => $element) {
            if ($matcher->match($index)) {
                $results[] = $element;
            }
        }

        return $results;
    }

    private function fetchProperties(Iterator $iterator, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createObjectIterator($iterator) as $name => $property) {
            if ($matcher->match($name)) {
                $results[] = $property;
            }
        }

        return $results;
    }

    /**
     * @param ValueListInterface $valueList
     * @return int[][]
     */
    public function fetchIndice(ValueListInterface $valueList): array
    {
        $result = [];
        foreach ($valueList->getValues() as $valueIndex => $value) {
            if (!$value instanceof ArrayValueInterface) {
                $result[$valueIndex] = [];
                continue;
            }

            $indice = [];
            $elementIterator = $this->valueIteratorFactory->createArrayIterator($value->createIterator());
            foreach ($elementIterator as $index => $element) {
                $indice[] = $index;
            }
            $result[$valueIndex] = $indice;
        }

        return $result;
    }

    /**
     * @param ValueListInterface $valueList
     * @param int|null $start
     * @param int|null $end
     * @param int|null $step
     * @return int[][]
     */
    public function fetchSliceIndice(ValueListInterface $valueList, ?int $start, ?int $end, ?int $step): array
    {
        $fullIndexList = $this->fetchIndice($valueList);

        $result = [];
        foreach ($fullIndexList as $valueIndex => $allIndice) {
            if (!isset($step)) {
                $step = 1;
            }
            $isReverse = $step < 0;
            $indexCount = count($allIndice);
            if (!isset($start)) {
                $start = $isReverse ? -1 : 0;
            }
            if ($start < 0) {
                $start = max($start + $indexCount, 0);
            }
            if (!isset($end)) {
                $end = $isReverse ? -$indexCount - 1 : $indexCount;
            }
            if ($end > $indexCount) {
                $end = $indexCount;
            }
            if ($end < 0) {
                $end = max($end + $indexCount, $isReverse ? -1 : 0);
            }
            $indice = [];
            $index = $start;
            while ($isReverse ? $index > $end : $index < $end) {
                $indice[] = $allIndice[$index];
                $index += $step;
            }
            $result[] = $indice;
        }

        return $result;
    }
}
