<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Path\PathInterface;

final class NonExistingSelectOnePathResult implements SelectOnePathResultInterface
{

    public function exists(): bool
    {
        return false;
    }

    public function get(): PathInterface
    {
        throw new Exception\ValueNotFoundException;
    }

    public function encode(): string
    {
        throw new Exception\ValueNotFoundException;
    }
}
