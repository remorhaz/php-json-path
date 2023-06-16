<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

interface EvaluatedValueListInterface extends ValueListInterface
{
    /**
     * @return list<bool>
     */
    public function getResults(): array;

    public function getResult(int $index): bool;
}
