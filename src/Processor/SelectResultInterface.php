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
    public function encode(): array;

    /**
     * @return string[]
     * @deprecated
     */
    public function toJson(): array;
}
