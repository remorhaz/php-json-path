<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

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

final class ValueIteratorFactory implements ValueIteratorFactoryInterface
{

    /**
     * @param Iterator $iterator
     * @return Generator|ValueInterface[]
     */
    public function createArrayIterator(Iterator $iterator): Generator
    {
        $event = $this->fetchEvent($iterator);
        if (!$event instanceof BeforeArrayEventInterface) {
            throw new Exception\UnexpectedDataEventException($event);
        }

        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof AfterArrayEventInterface) {
                return;
            }

            if (!$event instanceof ElementEventInterface) {
                throw new Exception\UnexpectedDataEventException($event);
            }
            $index = $event->getIndex();

            yield $index => $this->fetchValue($iterator);
        } while (true);
    }

    /**
     * @param Iterator $iterator
     * @return Generator|ValueInterface[]
     */
    public function createObjectIterator(Iterator $iterator): Generator
    {
        $event = $this->fetchEvent($iterator);
        if (!$event instanceof BeforeObjectEventInterface) {
            throw new Exception\UnexpectedDataEventException($event);
        }

        do {
            $event = $this->fetchEvent($iterator);
            if ($event instanceof AfterObjectEventInterface) {
                return;
            }

            if (!$event instanceof PropertyEventInterface) {
                throw new Exception\UnexpectedDataEventException($event);
            }
            $property = $event->getName();

            yield $property => $this->fetchValue($iterator);
        } while (true);
    }

    private function fetchEvent(Iterator $iterator): DataEventInterface
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

    private function skipValue(Iterator $iterator): void
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

    public function fetchValue(Iterator $iterator): ValueInterface
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
}
