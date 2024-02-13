<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser\Exception;

use RuntimeException;
use Throwable;

final class QueryAstNotBuiltException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private string $source,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Failed to build AST from JSONPath query: $this->source", previous: $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
