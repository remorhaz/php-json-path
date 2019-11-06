<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Walker\MutationInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class DeleteMutation implements MutationInterface
{

    private $paths;

    public function __construct(PathInterface ...$paths)
    {
        $this->paths = $paths;
    }

    public function __invoke(EventInterface $event, ValueWalkerInterface $valueWalker): Iterator
    {
        return $this->createEventGenerator($event);
    }

    public function reset(): void
    {
    }

    private function createEventGenerator(EventInterface $event): Generator
    {
        foreach ($this->paths as $path) {
            if ($path->contains($event->getPath())) {
                return;
            }
        }
        yield $event;
    }
}
