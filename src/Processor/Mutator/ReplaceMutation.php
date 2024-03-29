<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\AfterPropertyEventInterface;
use Remorhaz\JSON\Data\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Data\Event\BeforeElementEventInterface;
use Remorhaz\JSON\Data\Event\BeforeObjectEventInterface;
use Remorhaz\JSON\Data\Event\BeforePropertyEventInterface;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Event\ScalarEventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

use function array_reverse;
use function array_values;
use function count;

final class ReplaceMutation implements MutationInterface
{
    /**
     * @var list<PathInterface>
     */
    private array $paths;

    public function __construct(
        private readonly NodeValueInterface $newNode,
        PathInterface ...$paths,
    ) {
        $this->paths = $this->getNonNestedPaths(...$paths);
    }

    /**
     * @param PathInterface ...$paths
     * @return list<PathInterface>
     */
    private function getNonNestedPaths(PathInterface ...$paths): array
    {
        foreach ($this->createPathPairIterator(...$paths) as $pathPair) {
            [$parentPath, $nestedPath] = $pathPair;
            if ($parentPath->contains($nestedPath)) {
                throw new Exception\ReplaceAtNestedPathsException($parentPath, $nestedPath);
            }
        }

        return array_values($paths);
    }

    /**
     * @param PathInterface ...$paths
     * @return Iterator<list{PathInterface, PathInterface}>
     */
    private function createPathPairIterator(PathInterface ...$paths): Iterator
    {
        $pathsCount = count($paths);
        for ($i = 0; $i < $pathsCount; $i++) {
            for ($j = $i + 1; $j < $pathsCount; $j++) {
                $pathPair = [$paths[$i], $paths[$j]];
                yield $pathPair;
                yield array_reverse($pathPair);
            }
        }
    }

    /**
     * @param EventInterface       $event
     * @param ValueWalkerInterface $valueWalker
     * @return Iterator<EventInterface>
     */
    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event, $valueWalker);
    }

    public function reset(): void
    {
    }

    /**
     * @param EventInterface       $event
     * @param ValueWalkerInterface $valueWalker
     * @return Iterator<EventInterface>
     */
    private function createEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        foreach ($this->paths as $path) {
            if ($path->equals($event->getPath())) {
                yield from $this->createReplaceEventGenerator($event, $valueWalker);

                return;
            }
            if ($path->contains($event->getPath())) {
                return;
            }
        }
        yield $event;
    }

    /**
     * @param EventInterface       $event
     * @param ValueWalkerInterface $valueWalker
     * @return Iterator<EventInterface>
     */
    private function createReplaceEventGenerator(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        switch (true) {
            case $event instanceof BeforeElementEventInterface:
            case $event instanceof BeforePropertyEventInterface:
            case $event instanceof AfterElementEventInterface:
            case $event instanceof AfterPropertyEventInterface:
                yield $event;
                break;

            case $event instanceof ScalarEventInterface:
            case $event instanceof BeforeArrayEventInterface:
            case $event instanceof BeforeObjectEventInterface:
                yield from $valueWalker
                    ->createEventIterator($this->newNode, $event->getPath());
                break;
        }
    }
}
