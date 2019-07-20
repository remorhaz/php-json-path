<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser\Exception;

use RuntimeException;
use Throwable;

final class QueryAstNotBuiltException extends RuntimeException implements ExceptionInterface
{

    private $path;

    public function __construct(string $path, Throwable $previous = null)
    {
        $this->path = $path;
        parent::__construct("Failed to build AST from query: {$path}", 0, $previous);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
