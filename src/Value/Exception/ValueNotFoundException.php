<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use OutOfRangeException;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Throwable;

final class ValueNotFoundException extends OutOfRangeException implements ExceptionInterface
{

    private $index;

    private $valueList;

    public function __construct(int $index, ValueListInterface $valueList, Throwable $previous = null)
    {
        $this->index = $index;
        $this->valueList = $valueList;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Value not found in list at position {$this->index}";
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getValueList(): ValueListInterface
    {
        return $this->valueList;
    }
}
