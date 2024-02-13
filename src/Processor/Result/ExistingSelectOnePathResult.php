<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;

final class ExistingSelectOnePathResult implements SelectOnePathResultInterface
{
    public function exists(): bool
    {
        return true;
    }

    public function __construct(
        private readonly PathEncoderInterface $encoder,
        private readonly PathInterface $path,
    ) {
    }

    public function get(): PathInterface
    {
        return $this->path;
    }

    public function encode(): string
    {
        return $this
            ->encoder
            ->encodePath($this->path);
    }
}
