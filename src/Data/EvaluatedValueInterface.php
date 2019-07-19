<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

interface EvaluatedValueInterface extends ScalarValueInterface
{

    public function getData(): bool;
}
