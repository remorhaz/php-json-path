<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueResultInterface extends OneResultInterface
{
    public function encode(): string;

    public function decode(): mixed;

    public function get(): ValueInterface;
}
