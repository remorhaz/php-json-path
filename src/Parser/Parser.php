<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;

final class Parser implements ParserInterface
{

    private $ll1ParserFactory;

    public function __construct(Ll1ParserFactoryInterface $ll1ParserFactory)
    {
        $this->ll1ParserFactory = $ll1ParserFactory;
    }

    public function buildQueryAst(string $path): Tree
    {
        $queryAst = new Tree;
        $this
            ->ll1ParserFactory
            ->createParser($path, $queryAst)
            ->run();

        return $queryAst;
    }
}