<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_push;
use Iterator;
use function max;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilterInterface;

final class Fetcher
{

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
    }

    /**
     * @param NodeValueListInterface $source
     * @param ChildMatcherInterface ...$matcherList
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        NodeValueListInterface $source,
        ChildMatcherInterface ...$matcherList
    ): NodeValueListInterface {
        $values = [];
        $indexMap = [];
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $matcher = $matcherList[$sourceIndex];
            $children = $this->fetchValueChildren($matcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $indexMap[] = $source->getIndexMap()->getOuterIndex($sourceIndex);
            }
        }

        return new NodeValueList(new IndexMap(...$indexMap), ...$values);
    }

    public function fetchDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueListInterface $source
    ): NodeValueListInterface {
        $values = [];
        $indexMap = [];
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchValueDeepChildren($matcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $indexMap[] = $source->getIndexMap()->getOuterIndex($sourceIndex);
            }
        }

        return new NodeValueList(new IndexMap(...$indexMap), ...$values);
    }

    /**
     * @param ChildMatcherInterface $matcher
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

        throw new Exception\InvalidValueException($value);
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

        throw new Exception\InvalidValueException($value);
    }

    private function fetchDeepElements(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIterator->createArrayIterator($iterator) as $index => $element) {
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

    private function fetchDeepProperties(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIterator->createObjectIterator($iterator) as $name => $property) {
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
        $values = [];
        $indexMap = [];
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            if (!$sourceValue instanceof NodeValueInterface) {
                throw new Exception\InvalidContextValueException($sourceValue);
            }
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            $children = $sourceValue instanceof ArrayValueInterface
                ? $this->fetchValueChildren(new AnyChildMatcher, $sourceValue)
                : [$sourceValue];
            foreach ($children as $child) {
                $values[] = $child;
                $indexMap[] = $outerIndex;
            }
        }

        return new NodeValueList(new IndexMap(...$indexMap), ...$values);
    }

    private function fetchElements(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIterator->createArrayIterator($iterator) as $index => $element) {
            if ($matcher->match($index)) {
                $results[] = $element;
            }
        }

        return $results;
    }

    private function fetchProperties(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIterator->createObjectIterator($iterator) as $name => $property) {
            if ($matcher->match($name)) {
                $results[] = $property;
            }
        }

        return $results;
    }

    public function filterValues(
        ValueListFilterInterface $matcher,
        NodeValueListInterface $values
    ): NodeValueListInterface {
        return $matcher->filterValues($values);
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
            $elementIterator = $this->valueIterator->createArrayIterator($value->createIterator());
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
