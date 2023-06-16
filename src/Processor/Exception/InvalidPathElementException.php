<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidPathElementException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private mixed $pathElement,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        $type = gettype($this->pathElement);

        return "Invalid path element type: $type";
    }

    public function getPathElement(): mixed
    {
        return $this->pathElement;
    }
}
