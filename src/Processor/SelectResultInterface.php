<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

interface SelectResultInterface
{

    /**
     * @return mixed
     */
    public function decode();

    /**
     * @return string[]
     */
    public function asJson(): array;
}
