<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Value\ValueInterface;

final class NonExistingValueResult implements ValueResultInterface
{

    public function exists(): bool
    {
        return false;
    }

    public function encode(): string
    {
        throw new Exception\SelectedValueNotFoundException();
    }

    public function decode()
    {
        throw new Exception\SelectedValueNotFoundException();
    }

    public function get(): ValueInterface
    {
        throw new Exception\SelectedValueNotFoundException();
    }
}
