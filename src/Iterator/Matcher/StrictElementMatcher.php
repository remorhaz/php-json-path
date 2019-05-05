<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use function in_array;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indices;

    public function __construct(int ...$indices)
    {
        $this->indices = $indices;
    }

    public function match($address): bool
    {
        return in_array($address, $this->indices, true);
    }
}
