<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Path\PathInterface;

interface PathEncoderInterface
{
    public function encodePath(PathInterface $path): string;
}
