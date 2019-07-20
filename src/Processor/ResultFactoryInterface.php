<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Value\ValueListInterface;

interface ResultFactoryInterface
{

    public function createResult(ValueListInterface $values): SelectResultInterface;
}
