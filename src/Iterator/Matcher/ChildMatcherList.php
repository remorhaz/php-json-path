<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use function array_fill_keys;

final class ChildMatcherList
{

    /**
     * @param ChildMatcherInterface $matcher
     * @param int ...$valueIndice
     * @return ChildMatcherInterface[]
     */
    public static function populate(ChildMatcherInterface $matcher, int ...$valueIndice): array
    {
        return array_fill_keys($valueIndice, $matcher);
    }
}
