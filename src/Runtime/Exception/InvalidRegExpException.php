<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use RuntimeException;
use Throwable;

final class InvalidRegExpException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly string $pattern,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Error processing regular expression: $this->pattern", previous: $previous);
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
