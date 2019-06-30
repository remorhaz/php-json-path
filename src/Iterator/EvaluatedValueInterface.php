<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface EvaluatedValueInterface extends ScalarValueInterface
{

    public function getData(): bool;
}
