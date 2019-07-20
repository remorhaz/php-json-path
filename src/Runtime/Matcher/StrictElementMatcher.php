<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indice;

    public function __construct(int ...$indice)
    {
        $this->indice = $indice;
    }

    public function match($address): bool
    {
        return in_array($address, $this->indice, true);
    }
}
