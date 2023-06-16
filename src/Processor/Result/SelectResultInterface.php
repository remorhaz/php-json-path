<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Value\ValueInterface;

interface SelectResultInterface
{
    /**
     * @return list<mixed>
     */
    public function decode(): array;

    /**
     * @return list<string>
     */
    public function encode(): array;

    /**
     * @return list<ValueInterface>
     */
    public function get(): array;
}
