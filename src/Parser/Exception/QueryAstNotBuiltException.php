<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser\Exception;

use RuntimeException;
use Throwable;

final class QueryAstNotBuiltException extends RuntimeException implements ExceptionInterface
{

    private $source;

    public function __construct(string $source, Throwable $previous = null)
    {
        $this->source = $source;
        parent::__construct("Failed to build AST from JSONPath query: {$source}", 0, $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
