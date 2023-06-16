<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Path\PathInterface;

interface SelectOnePathResultInterface extends OneResultInterface
{
    public function get(): PathInterface;

    /**
     * Returns definite JSONPath query.
     */
    public function encode(): string;
}
