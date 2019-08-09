<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

final class EvaluatedValue implements EvaluatedValueInterface
{

    private $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function getData(): bool
    {
        return $this->value;
    }
}
