<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Iterator;
use Remorhaz\JSON\Data\Event\EventInterface;

interface MutationInterface
{
    public function __invoke(EventInterface $event): Iterator;
}
