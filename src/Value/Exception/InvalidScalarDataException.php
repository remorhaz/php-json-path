<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use DomainException;
use Remorhaz\JSON\Data\Value\DataAwareInterface;
use Throwable;

final class InvalidScalarDataException extends DomainException implements
    ExceptionInterface,
    DataAwareInterface
{
    public function __construct(
        private readonly mixed $data,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), previous: $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid scalar data";
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
