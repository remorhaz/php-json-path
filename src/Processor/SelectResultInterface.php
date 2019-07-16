<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

interface SelectResultInterface
{

    public function decode();

    /**
     * @return string[]
     */
    public function asJson(): array;
}
