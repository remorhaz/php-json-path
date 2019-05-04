<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

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
        $outerMap = [];
        $nextInnerIndex = 0;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchNodeValueChildren($matcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $outerMap[$nextInnerIndex++] = $source->getOuterIndex($sourceIndex);
            }
        }

        return new ValueList($outerMap, ...$values);
    }

    public function fetchFilterContext(ValueListInterface $source): ValueListInterface
    {
        $values = [];
        $outerMap = [];
        $nextInnerIndex = 0;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            if (!$sourceValue instanceof NodeValueInterface) {
                throw new Exception\InvalidContextValueException($sourceValue);
            }
            $outerIndex = $source->getOuterIndex($sourceIndex);
            $event = $this->fetchEvent($sourceValue->createIterator());
            if (!$event instanceof BeforeArrayEventInterface) {
                $values[] = $sourceValue;
                $outerMap[$nextInnerIndex++] = $outerIndex;
                continue;
            }

            $children = $this->fetchNodeValueChildren(new AnyChildMatcher, $sourceValue);
            foreach ($children as $child) {
                $values[] = $child;
                $outerMap[$nextInnerIndex++] = $outerIndex;
            }
        }

        return new ValueList($outerMap, ...$values);
    }

    /**
     * @param ChildMatcherInterface $matcher
     * @param ValueInterface $value
     * @return ValueInterface[]
     */
    private function fetchNodeValueChildren(
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

    /**
     * @param ValueListInterface $valueList
     * @return ValueListInterface
     * @deprecated
     */
    public function asLogicalValueList(ValueListInterface $valueList): ValueListInterface
    {
        $logicalValues = [];
        foreach ($valueList->getValues() as $value) {
            $logicalValues[] = new LiteralScalarValue(true);
        }

        return new ValueList($valueList->getOuterMap(), ...$logicalValues);
    }

    public function logicalOr(ValueListInterface $leftValueList, ValueListInterface $rightValueList): ValueListInterface
    {
        $values = [];
        $innerMap = [];
        $nextValueIndex = 0;
        /** @var ValueListInterface $valueList */
        foreach ([$leftValueList, $rightValueList] as $valueList) {
            foreach ($valueList->getValues() as $index => $value) {
                $outerIndex = $valueList->getOuterIndex($index);
                if (isset($innerMap[$outerIndex])) {
                    continue;
                }
                $values[] = new LiteralScalarValue(true);
                $innerMap[$outerIndex] = $nextValueIndex++;
            }
        }

        return new ValueList(\array_flip($innerMap), ...$values);
    }

    public function logicalAnd(
        ValueListInterface $leftValueList,
        ValueListInterface $rightValueList
    ): ValueListInterface {
        $values = [];
        $innerMap = [];
        $nextValueIndex = 0;
        foreach ($leftValueList->getValues() as $index => $value) {
            $outerIndex = $leftValueList->getOuterIndex($index);
            if (!$rightValueList->outerIndexExists($outerIndex)) {
                continue;
            }
            $values[] = new LiteralScalarValue(true);
            $innerMap[$outerIndex] = $nextValueIndex++;
        }

        return new ValueList(\array_flip($innerMap), ...$values);
    }

    public function isEqual(ValueListInterface $leftValueList, ValueListInterface $rightValueList): ValueListInterface
    {
        $values = [];
        $innerMap = [];
        $nextInnerIndex = 0;
        foreach ($leftValueList->getValues() as $leftInnerIndex => $leftValue) {
            $leftOuterIndex = $leftValueList->getOuterIndex($leftInnerIndex);

            foreach ($rightValueList->getValues() as $rightInnerIndex => $rightValue) {
                $rightOuterIndex = $rightValueList->getOuterIndex($rightInnerIndex);
                if ($leftOuterIndex != $rightOuterIndex) {
                    continue;
                }

                $isEqualEvent = $this->isEqualEvent(
                    $this->fetchEvent($leftValue->createIterator()),
                    $this->fetchEvent($rightValue->createIterator())
                );
                if (!$isEqualEvent) {
                    continue;
                }
                $values[] = new LiteralScalarValue(true);
                $innerMap[$leftOuterIndex] = $nextInnerIndex++;
            }
        }

        return new ValueList(\array_flip($innerMap), ...$values);
    }

    private function isEqualEvent(DataEventInterface $leftEvent, DataEventInterface $rightEvent): bool
    {
        if ($leftEvent instanceof ScalarEventInterface && $rightEvent instanceof ScalarEventInterface) {
            return $leftEvent->getData() === $rightEvent->getData();
        }

        return false;
    }

    public function createScalarList(ValueListInterface $valueList, $data): ValueListInterface
    {
        if (null !== $data && !\is_scalar($data)) {
            throw new Exception\NonScalarDataException($data);
        }

        return new ValueList(
            $valueList->getOuterMap(),
            ...\array_map(
                function () use ($data) {
                    return new LiteralScalarValue($data);
                },
                $valueList->getOuterMap()
            )
        );
    }
}
