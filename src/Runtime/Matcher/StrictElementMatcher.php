<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indexes;

    public function __construct(int ...$indexes)
    {
        $this->indexes = $indexes;
    }

    public function match($address, NodeValueInterface $value, NodeValueInterface $container): bool
    {
        return in_array($address, $this->indexes, true);
    }
}
