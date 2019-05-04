<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_flip;
use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;
use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilterInterface;

final class Fetcher
{

    public function fetchEvent(Iterator $iterator): DataEventInterface
    {
        if (!$iterator->valid()) {
            throw new Exception\UnexpectedEndOfData();
        }
        $event = $iterator->current();
        $iterator->next();

        if (!$event instanceof DataEventInterface) {
            throw new Exception\InvalidDataEventException($event);
        }

        return $event;
    }

    public function skipValue(Iterator $iterator): void
    {
        $event = $this->fetchEvent($iterator);
        if ($event instanceof ScalarEventInterface) {
            return;
        }

        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($iterator);
            return;
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($iterator);
            return;
        }

        throw new Exception\InvalidDataEventException($event);
    }

    public function fetchValue(Iterator $iterator): NodeValueInterface
    {
        $event = $this->fetchEvent($iterator);
        if ($event instanceof ScalarEventInterface) {
            return $event->getValue();
        }
        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($iterator);
            return $event->getValue();
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($iterator);
            return $event->getValue();
        }

        throw new Exception\InvalidDataEventException($event);
    }


    /**
     * @param ChildMatcherInterface $matcher
     * @param ValueListInterface $source
     * @return ValueListInterface
     */
    public function fetchChildren(
        Matcher\ChildMatcherInterface $matcher,
        ValueListInterface $source
    ): ValueListInterface {
        $values = [];
        $indexMap = [];
        $nextInnerIndex = 0;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchValueChildren($matcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $indexMap[$nextInnerIndex++] = $source->getOuterIndex($sourceIndex);
            }
        }

        return new NodeValueList($indexMap, ...$values);
    }

    /**
     * @param ChildMatcherInterface $matcher
     * @param ValueInterface $value
     * @return ValueInterface[]
     */
    private function fetchValueChildren(
        Matcher\ChildMatcherInterface $matcher,
        ValueInterface $value
    ): array {
        $iterator = $value->createIterator();
        $event = $this->fetchEvent($iterator);
        if ($event instanceof ScalarEventInterface) {
            return [];
        }

        if ($event instanceof BeforeArrayEventInterface) {
            return $this->fetchElements($iterator, $matcher);
        }

        if ($event instanceof BeforeObjectEventInterface) {
            return $this->fetchProperties($iterator, $matcher);
        }

        throw new Exception\InvalidDataEventException($event);
    }

    public function fetchFilterContext(ValueListInterface $source): ValueListInterface
    {
        $values = [];
        $indexMap = [];
        $nextInnerIndex = 0;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            if (!$sourceValue instanceof NodeValueInterface) {
                throw new Exception\InvalidContextValueException($sourceValue);
            }
            $outerIndex = $source->getOuterIndex($sourceIndex);
            $event = $this->fetchEvent($sourceValue->createIterator());
            if (!$event instanceof BeforeArrayEventInterface) {
                $values[] = $sourceValue;
                $indexMap[$nextInnerIndex++] = $outerIndex;
                continue;
            }

            $children = $this->fetchValueChildren(new AnyChildMatcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $indexMap[$nextInnerIndex++] = $outerIndex;
            }
        }

        return new NodeValueList($indexMap, ...$values);
    }

    private function fetchElements(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof ElementEventInterface) {
                if ($matcher->match($event)) {
                    $results[] = $this->fetchValue($iterator);
                    continue;
                }
                $this->skipValue($iterator);
                continue;
            }
            if ($event instanceof AfterArrayEventInterface) {
                return $results;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    public function filterValues(ValueListFilterInterface $matcher, ValueListInterface $values): ValueListInterface
    {
        return $matcher->filterValues($values);
    }

    private function fetchProperties(Iterator $iterator, ChildMatcherInterface $matcher): array
    {
        $results = [];
        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof PropertyEventInterface) {
                if ($matcher->match($event)) {
                    $results[] = $this->fetchValue($iterator);
                    continue;
                }
                $this->skipValue($iterator);
                continue;
            }
            if ($event instanceof AfterObjectEventInterface) {
                return $results;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    private function skipArrayValue(Iterator $iterator): void
    {
        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof ElementEventInterface) {
                $this->skipValue($iterator);
                continue;
            }
            if ($event instanceof AfterArrayEventInterface) {
                return;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    private function skipObjectValue(Iterator $iterator): void
    {
        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof PropertyEventInterface) {
                $this->skipValue($iterator);
                continue;
            }
            if ($event instanceof AfterObjectEventInterface) {
                return;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    public function logicalOr(
        ResultValueListInterface $leftValueList,
        ResultValueListInterface $rightValueList
    ): ResultValueListInterface {
        if ($leftValueList->getIndexMap() !== $rightValueList->getIndexMap()) {
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
        if ($leftValueList->getIndexMap() !== $rightValueList->getIndexMap()) {
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
        if ($leftValueList->getIndexMap() !== $rightValueList->getIndexMap()) {
            throw new Exception\InvalidIndexMapException($rightValueList);
        }
        $results = [];

        $rightValues = $rightValueList->getValues();
        foreach ($leftValueList->getValues() as $index => $leftValue) {
            $rightValue = $rightValues[$index];
            $results[] = $this->isEqualValue($leftValue, $rightValue);
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

        $results = [];
        foreach ($sourceValues->getIndexMap() as $outerIndex) {
            $results[] = $resultValues->outerIndexExists($outerIndex);
        }

        return new ResultValueList($sourceValues->getIndexMap(), ...$results);
    }
}
