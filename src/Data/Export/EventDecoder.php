<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Iterator;
use Remorhaz\JSON\Data\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\AfterObjectEventInterface;
use Remorhaz\JSON\Data\Event\AfterPropertyEventInterface;
use Remorhaz\JSON\Data\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Data\Event\BeforeElementEventInterface;
use Remorhaz\JSON\Data\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Data\Event\BeforePropertyEventInterface;
use Remorhaz\JSON\Data\Event\ScalarEventInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class EventDecoder implements EventDecoderInterface
{

    public function exportEvents(Iterator $events): ?NodeValueInterface
    {
        $buffer = [];
        $structures = [];
        $structure = null;
        foreach ($events as $event) {
            switch (true) {
                case $event instanceof ScalarEventInterface:
                    $buffer[] = $event->getData();
                    break;

                case $event instanceof BeforeArrayEventInterface:
                case $event instanceof BeforeObjectEventInterface:
                    $structures[] = $structure;
                    $structure = [];
                    break;

                case $event instanceof BeforeElementEventInterface:
                case $event instanceof BeforePropertyEventInterface:
                    break;

                case $event instanceof AfterElementEventInterface:
                    $structure[$event->getIndex()] = array_pop($buffer);
                    break;

                case $event instanceof AfterPropertyEventInterface:
                    $structure[$event->getName()] = array_pop($buffer);
                    break;

                case $event instanceof AfterArrayEventInterface:
                    $buffer[] = $structure;
                    $structure = array_pop($structures);
                    break;

                case $event instanceof AfterObjectEventInterface:
                    $buffer[] = (object) $structure;
                    $structure = array_pop($structures);
                    break;

                default:
                    throw new Exception\UnknownEventException($event);
            }
        }
        if (empty($buffer)) {
            return null;
        }
        $data = array_pop($buffer);
        if (empty($buffer)) {
            return (new NodeValueFactory)->createValue($data);
        }
        throw new Exception\InvalidDataBufferException($buffer);
    }
}
