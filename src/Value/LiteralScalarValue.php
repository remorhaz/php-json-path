<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ScalarValueInterface;

use function is_scalar;

final class LiteralScalarValue implements LiteralValueInterface, ScalarValueInterface
{
    private string|int|float|bool|null $data;

    public function __construct(mixed $data)
    {
        $this->data = null === $data || is_scalar($data)
            ? $data
            : throw new Exception\InvalidScalarDataException($data);
    }

    public function getData(): string|int|float|bool|null
    {
        return $this->data;
    }
}
