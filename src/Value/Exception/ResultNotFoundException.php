<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use OutOfRangeException;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Throwable;

final class ResultNotFoundException extends OutOfRangeException implements ExceptionInterface
{

    private $index;

    private $values;

    public function __construct(int $index, ValueListInterface $valueList, Throwable $previous = null)
    {
        $this->index = $index;
        $this->values = $valueList;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Result not found in list at position {$this->index}";
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getValues(): ValueListInterface
    {
        return $this->values;
    }
}
