<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson;

use Iterator;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;
use Remorhaz\JSON\Path\Iterator\Exception;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use stdClass;

final class EventExporter
{

    private $fetcher;

    public function __construct(Fetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function export(Iterator $iterator)
    {
        if (!$iterator->valid()) {
            $iterator->rewind();
        }
        $event = $this->fetcher->fetchEvent($iterator);
        if ($event instanceof ScalarEventInterface) {
            return $event->getData();
        }
        if ($event instanceof BeforeArrayEventInterface) {
            return $this->exportArrayData($iterator, $event->getPath());
        }

        if ($event instanceof BeforeObjectEventInterface) {
            return $this->exportObjectData($iterator, $event->getPath());
        }

        throw new Exception\UnexpectedDataEventException($event);
    }

    private function exportArrayData(Iterator $iterator, PathInterface $path): array
    {
        $result = [];

        do {
            $event = $this->fetcher->fetchEvent($iterator);
            if (!isset($event)) {
                throw new Exception\UnexpectedEndOfDataException;
            }
            if (!$path->equals($event->getPath())) {
                throw new Exception\UnexpectedDataEventException($event);
            }
            if ($event instanceof ElementEventInterface) {
                $result[$event->getIndex()] = $this->export($iterator);
                continue;
            }
            if ($event instanceof AfterArrayEventInterface) {
                break;
            }
        } while (true);

        return $result;
    }

    private function exportObjectData(Iterator $iterator, PathInterface $path): stdClass
    {
        $result = (object) [];

        do {
            $event = $this->fetcher->fetchEvent($iterator);
            if (!isset($event)) {
                throw new Exception\UnexpectedEndOfDataException;
            }
            if (!$path->equals($event->getPath())) {
                throw new Exception\UnexpectedDataEventException($event);
            }
            if ($event instanceof PropertyEventInterface) {
                $result->{$event->getName()} = $this->export($iterator);
                continue;
            }
            if ($event instanceof AfterObjectEventInterface) {
                break;
            }
        } while (true);

        return $result;
    }
}
