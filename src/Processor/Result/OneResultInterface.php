<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

interface OneResultInterface
{
    public function exists(): bool;
}
