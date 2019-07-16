<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

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
