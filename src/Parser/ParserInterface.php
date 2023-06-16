<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;

interface ParserInterface
{
    public function buildQueryAst(string $path): Tree;
}
