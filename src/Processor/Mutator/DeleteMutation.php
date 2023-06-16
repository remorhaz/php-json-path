<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

use function array_values;

final class DeleteMutation implements MutationInterface
{
    /**
     * @var list<PathInterface>
     */
    private array $paths;

    public function __construct(PathInterface ...$paths)
    {
        $this->paths = array_values($paths);
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
    }

    /**
     * @param EventInterface $event
     * @return Iterator<EventInterface>
     */
    private function createEventGenerator(EventInterface $event): Iterator
    {
        foreach ($this->paths as $path) {
            if ($path->contains($event->getPath())) {
                return;
            }
        }
        yield $event;
    }
}
