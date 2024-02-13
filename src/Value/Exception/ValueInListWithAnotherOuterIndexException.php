<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Throwable;

final class ValueInListWithAnotherOuterIndexException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly NodeValueInterface $value,
        private readonly int $expectedIndex,
        private readonly int $actualIndex,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), previous: $previous);
    }

    private function buildMessage(): string
    {
        return "Value is already in list with outer index $this->expectedIndex, not $this->actualIndex";
    }

    public function getValue(): NodeValueInterface
    {
        return $this->value;
    }

    public function getExpectedIndex(): int
    {
        return $this->expectedIndex;
    }

    public function getActualIndex(): int
    {
        return $this->actualIndex;
    }
}
