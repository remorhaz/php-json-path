<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_merge;
use ArrayIterator;
use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;
use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilterInterface;

final class Fetcher
{

    public function fetchEvent(Iterator $iterator, ?PathInterface $path = null): DataEventInterface
    {
        if (!$iterator->valid()) {
            throw new Exception\UnexpectedEndOfData();
        }
        $event = $iterator->current();
        $iterator->next();

        if (!$event instanceof DataEventInterface) {
            throw new Exception\InvalidDataEventException($event);
        }

        if (isset($path) && !$path->equals($event->getPath())) {
            throw new Exception\InvalidDataEventException($event);
        }

        return $event;
    }

    public function skipValue(Iterator $iterator, $path): void
    {
        $event = $this->fetchEvent($iterator, $path);
        if ($event instanceof ScalarEventInterface) {
            return;
        }

        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($iterator, $event->getPath());
            return;
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($iterator, $event->getPath());
            return;
        }

        throw new Exception\InvalidDataEventException($event);
    }

    public function fetchValue(Iterator $iterator, $path): ValueInterface
    {
        $event = $this->fetchEvent($iterator, $path);
        if ($event instanceof ScalarEventInterface) {
            return $event;
        }
        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($iterator, $event->getPath());
            return $event;
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($iterator, $event->getPath());
            return $event;
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
        $targetValues = [];
        $targetMap = [];
        $sourceMap = $source->getOuterMap();
        $targetIndex = 0;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this->fetchValueChildren($matcher, $sourceValue);
            foreach ($children as $child) {
                $targetValues[$targetIndex] = $child;
                $targetMap[$targetIndex++] = $sourceMap[$sourceIndex];
            }
        }

        return new ValueList($targetMap, ...$targetValues);
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
        $event = $this->fetchEvent($iterator, $value->getPath());
        if ($event instanceof ScalarEventInterface) {
            return [];
        }

        if ($event instanceof BeforeArrayEventInterface) {
            return $this->fetchElements($iterator, $matcher, $event->getPath());
        }

        if ($event instanceof BeforeObjectEventInterface) {
            return $this->fetchProperties($iterator, $matcher, $event->getPath());
        }

        throw new Exception\InvalidDataEventException($event);
    }

    private function fetchElements(Iterator $iterator, ChildMatcherInterface $matcher, PathInterface $path): array
    {
        $results = [];
        do {
            $event = $this->fetchEvent($iterator, $path);
            if ($event instanceof ElementEventInterface) {
                if ($matcher->match($event)) {
                    $results[] = $this->fetchValue($iterator, $event->getChildPath());
                    continue;
                }
                $this->skipValue($iterator, $event->getChildPath());
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

    private function fetchProperties(Iterator $iterator, ChildMatcherInterface $matcher, PathInterface $path): array
    {
        $results = [];
        do {
            $event = $this->fetchEvent($iterator, $path);
            if ($event instanceof PropertyEventInterface) {
                if ($matcher->match($event)) {
                    $results[] = $this->fetchValue($iterator, $event->getChildPath());
                    continue;
                }
                $this->skipValue($iterator, $event->getChildPath());
                continue;
            }
            if ($event instanceof AfterObjectEventInterface) {
                return $results;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    private function skipArrayValue(Iterator $iterator, PathInterface $path): void
    {
        do {
            $event = $this->fetchEvent($iterator, $path);
            if ($event instanceof ElementEventInterface) {
                $this->skipValue($iterator, $event->getChildPath());
                continue;
            }
            if ($event instanceof AfterArrayEventInterface) {
                return;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    private function skipObjectValue(Iterator $iterator, PathInterface $path): void
    {
        do {
            $event = $this->fetchEvent($iterator, $path);
            if ($event instanceof PropertyEventInterface) {
                $this->skipValue($iterator, $event->getChildPath());
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
            $logicalValues[] = new EventIteratorFactory(true, Path::createEmpty());
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
                $innerMap[$outerIndex] = $nextValueIndex;
                $values[$nextValueIndex++] = new EventIteratorFactory(true, Path::createEmpty());
            }
        }

        return new ValueList(\array_flip($innerMap), ...$values);
    }
}
