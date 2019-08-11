<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Generator;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;

final class EventGenerator
{

    private $stack;

    private $path;

    public function __construct(NodeValueInterface $value, PathInterface $path)
    {
        $this->stack = [$value];
        $this->path = $path;
    }

    public function __invoke(): Generator
    {
        while (true) {
            if (empty($this->stack)) {
                return;
            }
            $entity = array_pop($this->stack);
            switch (true) {
                case $entity instanceof EventInterface:
                    yield from $this->onEvent($entity);
                    break;

                case $entity instanceof ScalarValueInterface:
                    yield from $this->onScalarValue($entity);
                    break;

                case $entity instanceof ArrayValueInterface:
                    yield from $this->onArrayValue($entity);
                    break;

                case $entity instanceof ObjectValueInterface:
                    yield from $this->onObjectValue($entity);
                    break;

                default:
                    throw new Exception\UnexpectedEntityException($entity);
            }
        }
    }

    private function onEvent(EventInterface $event): Generator
    {
        switch (true) {
            case $event instanceof BeforeElementEventInterface:
                $this->path = $this
                    ->path
                    ->copyWithElement($event->getIndex());
                break;

            case $event instanceof BeforePropertyEventInterface:
                $this->path = $this
                    ->path
                    ->copyWithProperty($event->getName());
                break;

            case $event instanceof AfterElementEventInterface:
            case $event instanceof AfterPropertyEventInterface:
                $this->path = $this
                    ->path
                    ->copyParent();
                break;
        }
        yield $event;
    }

    private function onScalarValue(ScalarValueInterface $value): Generator
    {
        yield new ScalarEvent($value->getData(), $this->path);
    }

    private function onArrayValue(ArrayValueInterface $value): Generator
    {
        $localStack = [];
        foreach ($value->createChildIterator() as $index => $child) {
            $elementPath = $this
                ->path
                ->copyWithElement($index);
            array_push(
                $localStack,
                new BeforeElementEvent($index, $elementPath),
                $child,
                new AfterElementEvent($index, $elementPath)
            );
        }
        array_push(
            $this->stack,
            new AfterArrayEvent($this->path),
            ...array_reverse($localStack)
        );
        yield new BeforeArrayEvent($this->path);
    }

    private function onObjectValue(ObjectValueInterface $value): Generator
    {
        $localStack = [];
        foreach ($value->createChildIterator() as $name => $child) {
            $elementPath = $this
                ->path
                ->copyWithProperty($name);
            array_push(
                $localStack,
                new BeforePropertyEvent($name, $elementPath),
                $child,
                new AfterPropertyEvent($name, $elementPath)
            );
        }
        array_push(
            $this->stack,
            new AfterObjectEvent($this->path),
            ...array_reverse($localStack)
        );
        yield new BeforeObjectEvent($this->path);
    }
}
