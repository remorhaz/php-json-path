<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Data\Value\StructValueInterface;
use function iterator_count;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class ValueFetcher implements ValueFetcherInterface
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory)
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

        if ($value instanceof StructValueInterface) {
            return $this->fetchChildren($value, $matcher);
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

        if ($value instanceof StructValueInterface) {
            return $this->fetchDeepChildren($value, $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function fetchChildren(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        if (!$value instanceof StructValueInterface) {
            // TODO: extract correct argument type?
            throw new Exception\UnexpectedNodeValueFetchedException($value);
        }
        $results = [];
        foreach ($value->createChildIterator() as $index => $element) {
            if ($matcher->match($index, $element, $value)) {
                $results[] = $element;
            }
        }

        return $results;
    }

    private function fetchDeepChildren(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
    {
        if (!$value instanceof StructValueInterface) {
            // TODO: extract correct argument type?
            throw new Exception\UnexpectedNodeValueFetchedException($value);
        }

        $results = [];
        foreach ($value->createChildIterator() as $index => $element) {
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

    public function fetchArrayLength(NodeValueInterface $value): ?int
    {
        return $value instanceof ArrayValueInterface
            ? iterator_count($value->createChildIterator())
            : null;
    }
}
