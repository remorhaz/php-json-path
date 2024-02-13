<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;

interface Ll1ParserFactoryInterface
{
    public function createParser(string $source, Tree $queryAst): Ll1Parser;
}
