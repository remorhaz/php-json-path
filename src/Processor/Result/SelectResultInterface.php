<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Value\ValueInterface;

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
     * @return ValueInterface[]
     */
    public function get(): array;
}
