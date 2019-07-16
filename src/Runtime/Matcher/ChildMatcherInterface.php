<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

interface ChildMatcherInterface
{

    public function match($address): bool;
}
