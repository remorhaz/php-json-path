<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indice;

    public function __construct(int ...$indice)
    {
        $this->indice = $indice;
    }

    public function match($address, ValueInterface $value): bool
    {
        return in_array($address, $this->indice, true);
    }
}
