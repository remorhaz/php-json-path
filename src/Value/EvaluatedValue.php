<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

final class EvaluatedValue implements EvaluatedValueInterface
{
    public function __construct(
        private readonly bool $value,
    ) {
    }

    public function getData(): bool
    {
        return $this->value;
    }
}
