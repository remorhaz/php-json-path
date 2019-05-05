<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;
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
     * @param ChildMatcherInterface $matcher
     * @param NodeValueListInterface $source
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueListInterface $source
    ): NodeValueListInterface {
        $values = [];
        $indexMap = [];
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchValueChildren($matcher, $sourceValue);
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
}
