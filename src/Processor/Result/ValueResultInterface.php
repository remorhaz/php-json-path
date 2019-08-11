<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

interface ValueResultInterface extends OneResultInterface
{

    public function encode(): string;

    public function decode();
}
