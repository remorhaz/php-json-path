<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;
use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;

final class ValueIterator
{

    public function createArrayIterator(ArrayValueInterface $value): \Generator
    {
        $iterator = $value->createIterator();

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

    public function createObjectIterator(ObjectValueInterface $value): \Generator
    {
        $iterator = $value->createIterator();

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
