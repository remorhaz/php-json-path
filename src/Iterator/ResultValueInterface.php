<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface ResultValueInterface extends ScalarValueInterface
{

    public function getData(): bool;
}
