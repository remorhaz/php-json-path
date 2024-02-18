<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator\Exception;

use Throwable;
use UnexpectedValueException;

final class InvalidElementIndexException extends UnexpectedValueException implements ExceptionInterface
{
    public function __construct(
        private readonly ?string $index,
        ?Throwable $previous = null,
    ) {
        $indexText = isset($this->index) ? "'$this->index'" : 'NULL';
        parent::__construct(
            message: "Element index is not an integer: $indexText",
            previous: $previous,
        );
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }
}
