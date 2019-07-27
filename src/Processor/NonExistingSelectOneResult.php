<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

final class NonExistingSelectOneResult implements SelectOneResultInterface
{

    public function exists(): bool
    {
        return false;
    }

    public function encode(): string
    {
        throw new Exception\ValueNotFoundException;
    }

    public function decode()
    {
        throw new Exception\ValueNotFoundException;
    }
}
