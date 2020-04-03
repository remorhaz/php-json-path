<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Tree;

interface AstTranslatorInterface
{

    public function buildQuery(string $source, Tree $queryAst): QueryInterface;
}
