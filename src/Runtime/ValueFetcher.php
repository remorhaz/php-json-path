<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use function iterator_count;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class ValueFetcher implements ValueFetcherInterface
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface $value
     * @return NodeValueInterface[]
     */
    public function fetchValueChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array {
        if ($value instanceof ScalarValueInterface) {
            return [];
        }

        if ($value instanceof ArrayValueInterface) {
            return $this->fetchElements($value, $matcher);
        }

        if ($value instanceof ObjectValueInterface) {
            return $this->fetchProperties($value, $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    public function fetchValueDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): array {
        if ($value instanceof ScalarValueInterface) {
            return [];
        }

        if ($value instanceof ArrayValueInterface) {
            return $this->fetchDeepElements($value, $matcher);
        }

        if ($value instanceof ObjectValueInterface) {
            return $this->fetchDeepProperties($value, $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function fetchElements(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createArrayIterator($value->createIterator()) as $index => $element) {
            if ($matcher->match($index, $element, $value)) {
                $results[] = $element;
            }
        }

        return $results;
    }

    private function fetchProperties(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createObjectIterator($value->createIterator()) as $name => $property) {
            if ($matcher->match($name, $property, $value)) {
                $results[] = $property;
            }
        }

        return $results;
    }

    private function fetchDeepElements(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createArrayIterator($value->createIterator()) as $index => $element) {
            if ($matcher->match($index, $element, $value)) {
                $results[] = $element;
            }
            array_push(
                $results,
                ...$this->fetchValueDeepChildren($matcher, $element)
            );
        }

        return $results;
    }

    private function fetchDeepProperties(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        $results = [];
        foreach ($this->valueIteratorFactory->createObjectIterator($value->createIterator()) as $name => $property) {
            if ($matcher->match($name, $property, $value)) {
                $results[] = $property;
            }
            array_push(
                $results,
                ...$this->fetchValueDeepChildren($matcher, $property)
            );
        }

        return $results;
    }

    public function fetchArrayLength(NodeValueInterface $value): ?int
    {
        if (!$value instanceof ArrayValueInterface) {
            return null;
        }
        $elementIterator = $this->valueIteratorFactory->createArrayIterator($value->createIterator());

        return iterator_count($elementIterator);
    }
}
