<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Generator;
use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;
use Remorhaz\JSON\Data\Path\PathInterface;

final class DeleteMutation implements MutationInterface
{

    private $paths;

    public function __construct(PathInterface ...$paths)
    {
        $this->paths = $paths;
    }

    public function __invoke(EventInterface $event): Iterator
    {
        return $this->createEventGenerator($event);
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
