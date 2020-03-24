<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Iterator;
use Remorhaz\JSON\Data\Value\StructValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\SortedChildMatcherInterface;

use function ksort;
use const SORT_ASC;
use const SORT_NUMERIC;

final class ValueFetcher implements ValueFetcherInterface
{

    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface            $value
     * @return NodeValueInterface[]|Iterator
     */
    public function createChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        if ($value instanceof ScalarValueInterface) {
            return;
        }

        if ($value instanceof StructValueInterface) {
            yield from $matcher instanceof SortedChildMatcherInterface
                ? $this->createSortedChildStructIterator($matcher, $value)
                : $this->createUnsortedChildStructIterator($matcher, $value);

            return;
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    private function createUnsortedChildStructIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        if (!$value instanceof StructValueInterface) {
            return;
        }

        foreach ($value->createChildIterator() as $index => $element) {
            if ($matcher->match($index, $element, $value)) {
                yield $element;
            }
        }
    }

    private function createSortedChildStructIterator(
        Matcher\SortedChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        if (!$value instanceof StructValueInterface) {
            return;
        }

        $sortableElements = [];
        foreach ($value->createChildIterator() as $index => $element) {
            if ($matcher->match($index, $element, $value)) {
                $sortIndex = $matcher->getSortIndex($index, $element, $value);
                $sortableElements[$sortIndex] = $element;
            }
        }
        ksort($sortableElements, SORT_ASC | SORT_NUMERIC);
        yield from $sortableElements;
    }

    public function createDeepChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        if ($value instanceof ScalarValueInterface) {
            return;
        }

        if ($value instanceof StructValueInterface) {
            foreach ($value->createChildIterator() as $index => $element) {
                if ($matcher->match($index, $element, $value)) {
                    yield $element;
                }
                yield from $this->createDeepChildrenIterator($matcher, $element);
            }

            return;
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }
}
