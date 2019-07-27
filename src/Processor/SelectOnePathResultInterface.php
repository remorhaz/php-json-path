<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Path\PathInterface;

interface SelectOnePathResultInterface
{

    public function get(): PathInterface;

    /**
     * Returns definite JSONPath query.
     *
     * @return string
     */
    public function encode(): string;
}
