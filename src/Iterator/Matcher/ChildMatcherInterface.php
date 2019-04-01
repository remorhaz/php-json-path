<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\Event\ChildEventInterface;

interface ChildMatcherInterface
{

    public function match(ChildEventInterface $event): bool;
}
