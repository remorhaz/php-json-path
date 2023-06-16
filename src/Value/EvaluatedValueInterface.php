<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ScalarValueInterface;

interface EvaluatedValueInterface extends ScalarValueInterface
{
    public function getData(): bool;
}
