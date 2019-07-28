<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class StrictPropertyMatcher implements ChildMatcherInterface
{

    private $properties;

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }

    public function match($address, ValueInterface $value): bool
    {
        return in_array($address, $this->properties, true);
    }
}
