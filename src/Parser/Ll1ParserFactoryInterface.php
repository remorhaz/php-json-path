<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Parser\LL1\Parser;

interface Ll1ParserFactoryInterface
{

    public function createParser(string $path, Tree $queryAst): Parser;
}
