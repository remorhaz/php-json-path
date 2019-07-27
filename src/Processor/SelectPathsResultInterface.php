<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Path\PathInterface;

interface SelectPathsResultInterface
{

    /**
     * Returns list of paths.
     *
     * @return PathInterface[]
     */
    public function get(): array;

    /**
     * Returns list of definite JSONPath queries.
     *
     * @return string[]
     */
    public function encode(): array;
}
