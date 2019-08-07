<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class Parser implements ParserInterface
{

    private $ll1ParserFactory;

    public function __construct(Ll1ParserFactoryInterface $ll1ParserFactory)
    {
        $this->ll1ParserFactory = $ll1ParserFactory;
    }

    public function buildQueryAst(string $path): Tree
    {
        try {
            $queryAst = new Tree;
            $this
                ->ll1ParserFactory
                ->createParser($path, $queryAst)
                ->run();

            return $queryAst;
        } catch (Throwable $e) {
            throw new Exception\QueryAstNotBuiltException($path, $e);
        }
    }
}
