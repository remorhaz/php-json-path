<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Path\PathInterface;

interface SelectPathsResultInterface
{
    /**
     * Returns list of paths.
     *
     * @return list<PathInterface>
     */
    public function get(): array;

    /**
     * Returns list of definite JSONPath queries.
     *
     * @return list<string>
     */
    public function encode(): array;
}
