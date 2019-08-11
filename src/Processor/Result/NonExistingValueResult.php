<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

final class NonExistingValueResult implements ValueResultInterface
{

    public function exists(): bool
    {
        return false;
    }

    public function encode(): string
    {
        throw new Exception\SelectedValueNotFoundException;
    }

    public function decode()
    {
        throw new Exception\SelectedValueNotFoundException;
    }
}
