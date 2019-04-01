<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_merge;
use ArrayIterator;
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
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;

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
            return new Value(new ArrayIterator([$event]), $path);
        }
        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($iterator, $event->getPath());
            return new Value($event->getIterator(), $event->getPath());
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($iterator, $event->getPath());
            return new Value($event->getIterator(), $event->getPath());
        }

        throw new Exception\InvalidDataEventException($event);
    }


    /**
     * @param ChildMatcherInterface $matcher
     * @param ValueInterface ...$values
     * @return ValueInterface[]
     */
    public function fetchChildren(Matcher\ChildMatcherInterface $matcher, ValueInterface ...$values): array
    {
        $result = [];

        foreach ($values as $value) {
            $result = array_merge($result, $this->fetchValueChildren($matcher, $value));
        }

        return $result;
    }

    /**
     * @param ValueInterface $value
     * @param ChildMatcherInterface $matcher
     * @return ValueInterface[]
     */
    private function fetchValueChildren(Matcher\ChildMatcherInterface $matcher, ValueInterface $value): array
    {
        $event = $this->fetchEvent($value->getIterator(), $value->getPath());
        if ($event instanceof ScalarEventInterface) {
            return [];
        }

        if ($event instanceof BeforeArrayEventInterface) {
            return $this->fetchElements($value->getIterator(), $matcher, $event->getPath());
        }

        if ($event instanceof BeforeObjectEventInterface) {
            return $this->fetchProperties($value->getIterator(), $matcher, $event->getPath());
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
}
