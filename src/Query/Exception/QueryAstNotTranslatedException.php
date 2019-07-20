<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class QueryAstNotTranslatedException extends LogicException implements ExceptionInterface
{

    private $queryAst;

    public function __construct(Tree $queryAst, Throwable $previous = null)
    {
        $this->queryAst = $queryAst;
        parent::__construct("Query AST was not translated to callback function", 0, $previous);
    }

    public function getQueryAst(): Tree
    {
        return $this->queryAst;
    }
}
