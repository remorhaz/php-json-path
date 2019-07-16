<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Tree;

interface QueryAstTranslatorInterface
{

    public function buildQuery(Tree $queryAst): QueryInterface;
}
