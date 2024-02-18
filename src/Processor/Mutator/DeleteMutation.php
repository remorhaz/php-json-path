<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\AfterElementEventInterface;
use Remorhaz\JSON\Data\Event\ElementEventInterface;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;
use WeakMap;

use function array_key_last;
use function array_values;

final class DeleteMutation implements MutationInterface
{
    /**
     * @var list<PathInterface>
     */
    private array $paths;

    /**
     * @var list<PathInterface>
     */
    private array $parentsOfDeletedIndexes = [];

    /**
     * @var WeakMap<PathInterface, int>
     */
    private WeakMap $countsOfDeletedElements;

    public function __construct(PathInterface ...$paths)
    {
        $this->paths = array_values($paths);
        /** @var WeakMap<PathInterface, int> */
        $this->countsOfDeletedElements = new WeakMap();
    }

    /**
     * @param EventInterface       $event
     * @param ValueWalkerInterface $valueWalker
     * @return Iterator<EventInterface>
     */
    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event);
    }

    public function reset(): void
    {
        $this->parentsOfDeletedIndexes = [];
    }

    /**
     * @param EventInterface $event
     * @return Iterator<EventInterface>
     */
    private function createEventGenerator(EventInterface $event): Iterator
    {
        foreach ($this->paths as $path) {
            if ($path->equals($event->getPath())) {
                if ($event instanceof AfterElementEventInterface) {
                    $elementParentPath = $this->getParentOfDeletedElement($event->getPath());
                    $this->registerDeletedElement($elementParentPath);
                }
                return;
            }
            if ($path->contains($event->getPath())) {
                return;
            }
        }

        $indexesToReplace = $this->getIndexesToReplace($event->getPath());
        if (!empty($indexesToReplace)) {
            $lastIndex = $this->getLastPathIndex($event->getPath());
            $newPath = $this->replaceElementIndexes($event->getPath(), $indexesToReplace);
            $event = $event instanceof ElementEventInterface
                ? $event->with(
                    path: $newPath,
                    index: $indexesToReplace[$lastIndex] ?? null,
                )
                : $event->with(
                    path: $newPath,
                );
        }

        yield $event;
    }

    private function getLastPathIndex(PathInterface $path): int
    {
        $pathElements = $path->getElements();
        $lastIndex = array_key_last($pathElements);

        return $lastIndex ?? throw new Exception\InvalidElementIndexException(null);
    }

    /**
     * @param PathInterface $path
     * @return array<int, int>
     */
    private function getIndexesToReplace(PathInterface $path): array
    {
        $indexesToReplace = [];
        foreach ($this->parentsOfDeletedIndexes as $elementParentPath) {
            if ($elementParentPath->equals($path)) {
                continue;
            }
            if ($elementParentPath->contains($path)) {
                [$indexOffset, $indexValue] = $this->getElementIndexWithOffset($path, $elementParentPath);
                $indexesToReplace[$indexOffset] = $indexValue - $this->getCountOfDeletedElements($elementParentPath);
            }
        }

        return $indexesToReplace;
    }

    /**
     * @param PathInterface   $path
     * @param array<int, int> $indexesToReplace
     * @return PathInterface
     */
    private function replaceElementIndexes(PathInterface $path, array $indexesToReplace): PathInterface
    {
        foreach ($indexesToReplace as $indexOffset => $newIndex) {
            $path = $this->replaceElementIndex($path, $indexOffset, $newIndex);
        }

        return $path;
    }

    /**
     * @param PathInterface $path
     * @param PathInterface $elementParentPath
     * @return array{int, int}
     */
    private function getElementIndexWithOffset(PathInterface $path, PathInterface $elementParentPath): array
    {
        $indexOffset = \count($elementParentPath->getElements());
        $pathElements = $path->getElements();
        $index = $pathElements[$indexOffset] ?? null;

        return [
            $indexOffset,
            \is_int($index)
                ? $index
                : throw new Exception\InvalidElementIndexException($index),
        ];
    }

    private function replaceElementIndex(PathInterface $path, int $indexOffset, int $newIndex): PathInterface
    {
        $pathElements = $path->getElements();
        $pathElements[$indexOffset] = $newIndex;

        return new Path(...$pathElements);
    }

    private function registerDeletedElement(PathInterface $parentPath): void
    {
        $this->countsOfDeletedElements[$parentPath] = $this->getCountOfDeletedElements($parentPath) + 1;
    }

    private function getCountOfDeletedElements(PathInterface $parentPath): int
    {
        return $this->countsOfDeletedElements[$parentPath] ?? 0;
    }

    private function getParentOfDeletedElement(PathInterface $elementPath): PathInterface
    {
        $parentPath = $elementPath->copyParent();
        foreach ($this->parentsOfDeletedIndexes as $addedParentPath) {
            if ($parentPath->equals($addedParentPath)) {
                return $addedParentPath;
            }
        }

        return $this->parentsOfDeletedIndexes[] = $parentPath;
    }
}
