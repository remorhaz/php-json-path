<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class QueryAstNotTranslatedException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private Tree $queryAst,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Query AST was not translated to callback function", 0, $previous);
    }

    public function getQueryAst(): Tree
    {
        return $this->queryAst;
    }
}
