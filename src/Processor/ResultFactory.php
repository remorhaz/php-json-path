<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Value\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Data\Value\ValueListInterface;

final class ResultFactory implements ResultFactoryInterface
{

    private $valueIteratorFactoryInterface;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory)
    {
        $this->valueIteratorFactoryInterface = $valueIteratorFactory;
    }

    public function createResult(ValueListInterface $values): SelectResultInterface
    {
        return new SelectResult($this->valueIteratorFactoryInterface, ...$values->getValues());
    }
}
