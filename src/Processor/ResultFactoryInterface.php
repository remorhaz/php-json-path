<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Value\ValueListInterface;

interface ResultFactoryInterface
{

    public function createResult(ValueListInterface $values): SelectResultInterface;
}
