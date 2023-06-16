<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

final class EvaluatedValue implements EvaluatedValueInterface
{
    public function __construct(
        private bool $value,
    ) {
    }

    public function getData(): bool
    {
        return $this->value;
    }
}
