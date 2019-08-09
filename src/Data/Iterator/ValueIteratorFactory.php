<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Data\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Data\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Data\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Remorhaz\JSON\Data\Event\ElementEventInterface;
use Remorhaz\JSON\Data\Event\PropertyEventInterface;
use Remorhaz\JSON\Data\Event\ScalarEventInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class ValueIteratorFactory implements ValueIteratorFactoryInterface
{

    /**
     * @param Iterator $eventIterator
     * @return Iterator|ValueInterface[]
     */
    public function createArrayIterator(Iterator $eventIterator): Iterator
    {
        return $this->createArrayGenerator($eventIterator);
    }

    /**
     * @param Iterator $eventIterator
     * @return Generator|ValueInterface[]
     */
    private function createArrayGenerator(Iterator $eventIterator): Generator
    {
        $event = $this->fetchEvent($eventIterator);
        if (!$event instanceof BeforeArrayEventInterface) {
            throw new Exception\UnexpectedDataEventException($event, BeforeArrayEventInterface::class);
        }

        do {
            $event = $this->fetchEvent($eventIterator);
            if ($event instanceof AfterArrayEventInterface) {
                return;
            }

            if (!$event instanceof ElementEventInterface) {
                throw new Exception\UnexpectedDataEventException($event, ElementEventInterface::class);
            }
            $index = $event->getIndex();

            yield $index => $this->fetchValue($eventIterator);
        } while (true);
    }

    /**
     * @param Iterator $eventIterator
     * @return Iterator|ValueInterface[]
     */
    public function createObjectIterator(Iterator $eventIterator): Iterator
    {
        return $this->createObjectGenerator($eventIterator);
    }

    /**
     * @param Iterator $eventIterator
     * @return Generator|ValueInterface[]
     */
    public function createObjectGenerator(Iterator $eventIterator): Generator
    {
        $event = $this->fetchEvent($eventIterator);
        if (!$event instanceof BeforeObjectEventInterface) {
            throw new Exception\UnexpectedDataEventException($event, BeforeObjectEventInterface::class);
        }

        do {
            $event = $this->fetchEvent($eventIterator);
            if ($event instanceof AfterObjectEventInterface) {
                return;
            }

            if (!$event instanceof PropertyEventInterface) {
                throw new Exception\UnexpectedDataEventException($event, PropertyEventInterface::class);
            }
            $property = $event->getName();

            yield $property => $this->fetchValue($eventIterator);
        } while (true);
    }

    private function fetchEvent(Iterator $eventIterator): DataEventInterface
    {
        if (!$eventIterator->valid()) {
            throw new Exception\UnexpectedEndOfDataEventsException;
        }
        $event = $eventIterator->current();
        $eventIterator->next();

        if (!$event instanceof DataEventInterface) {
            throw new Exception\InvalidDataEventException($event);
        }

        return $event;
    }

    private function skipValue(Iterator $eventIterator): void
    {
        $event = $this->fetchEvent($eventIterator);
        if ($event instanceof ScalarEventInterface) {
            return;
        }

        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($eventIterator);
            return;
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($eventIterator);
            return;
        }

        throw new Exception\InvalidDataEventException($event);
    }

    private function fetchValue(Iterator $eventIterator): ValueInterface
    {
        $event = $this->fetchEvent($eventIterator);
        if ($event instanceof ScalarEventInterface) {
            return $event->getValue();
        }
        if ($event instanceof BeforeArrayEventInterface) {
            $this->skipArrayValue($eventIterator);
            return $event->getValue();
        }

        if ($event instanceof BeforeObjectEventInterface) {
            $this->skipObjectValue($eventIterator);
            return $event->getValue();
        }

        throw new Exception\InvalidDataEventException($event);
    }

    private function skipArrayValue(Iterator $eventIterator): void
    {
        do {
            $event = $this->fetchEvent($eventIterator);
            if ($event instanceof ElementEventInterface) {
                $this->skipValue($eventIterator);
                continue;
            }
            if ($event instanceof AfterArrayEventInterface) {
                return;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }

    private function skipObjectValue(Iterator $eventIterator): void
    {
        do {
            $event = $this->fetchEvent($eventIterator);
            if ($event instanceof PropertyEventInterface) {
                $this->skipValue($eventIterator);
                continue;
            }
            if ($event instanceof AfterObjectEventInterface) {
                return;
            }
            throw new Exception\InvalidDataEventException($event);
        } while (true);
    }
}
