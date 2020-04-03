<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use RuntimeException;
use Throwable;

final class InvalidRegExpException extends RuntimeException implements ExceptionInterface
{

    private $pattern;

    public function __construct(string $pattern, Throwable $previous = null)
    {
        $this->pattern = $pattern;
        parent::__construct("Error processing regular expression: {$this->pattern}", 0, $previous);
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
