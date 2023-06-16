<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use RuntimeException;
use Throwable;

final class InvalidRegExpException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private string $pattern,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Error processing regular expression: $this->pattern", 0, $previous);
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
