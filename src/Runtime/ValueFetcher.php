<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\StructValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class ValueFetcher implements ValueFetcherInterface
{

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
            return $this->fetchStructChildren($value, $matcher);
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
            return $this->fetchStructDeepChildren($value, $matcher);
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function fetchStructChildren(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
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

    private function fetchStructDeepChildren(NodeValueInterface $value, Matcher\ChildMatcherInterface $matcher): array
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
}
