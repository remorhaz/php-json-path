<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Value\StructValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class ValueFetcher implements ValueFetcherInterface
{

    /**
     * @param Matcher\ChildMatcherInterface $matcher
     * @param NodeValueInterface $value
     * @return NodeValueInterface[]|Iterator
     */
    public function createChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        return $this->createChildrenGenerator($matcher, $value);
    }

    private function createChildrenGenerator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Generator {
        if ($value instanceof ScalarValueInterface) {
            return;
        }

        if ($value instanceof StructValueInterface) {
            foreach ($value->createChildIterator() as $index => $element) {
                if ($matcher->match($index, $element, $value)) {
                    yield $element;
                }
            }
            return;
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }

    public function createDeepChildrenIterator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Iterator {
        return $this->createDeepChildrenGenerator($matcher, $value);
    }

    private function createDeepChildrenGenerator(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueInterface $value
    ): Generator {
        if ($value instanceof ScalarValueInterface) {
            return;
        }

        if ($value instanceof StructValueInterface) {
            foreach ($value->createChildIterator() as $index => $element) {
                if ($matcher->match($index, $element, $value)) {
                    yield $element;
                }
                yield from $this->createDeepChildrenGenerator($matcher, $element);
            }
            return;
        }

        throw new Exception\UnexpectedNodeValueFetchedException($value);
    }
}
