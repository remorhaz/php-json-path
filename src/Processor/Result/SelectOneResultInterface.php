<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

interface SelectOneResultInterface
{

    public function exists(): bool;

    public function encode(): string;

    public function decode();
}
