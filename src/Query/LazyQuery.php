<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\ValueListInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class LazyQuery implements QueryInterface
{

    private $loadedQuery;

    private $path;

    private $parser;

    private $astTranslator;

    public function __construct(string $path, ParserInterface $parser, QueryAstTranslatorInterface $astTranslator)
    {
        $this->path = $path;
        $this->parser = $parser;
        $this->astTranslator = $astTranslator;
    }

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface
    {
        return $this->getLoadedQuery()($runtime, $rootNode);
    }

    private function getLoadedQuery(): QueryInterface
    {
        if (!isset($this->loadedQuery)) {
            $this->loadedQuery = $this->loadQuery();
        }

        return $this->loadedQuery;
    }

    private function loadQuery(): QueryInterface
    {
        $queryAst = $this
            ->parser
            ->buildQueryAst($this->path);

        return $queryCallback = $this
            ->astTranslator
            ->buildQuery($queryAst);
    }
}
