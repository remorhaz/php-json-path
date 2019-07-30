<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class LazyQuery implements QueryInterface
{

    private $loadedQuery;

    private $source;

    private $parser;

    private $astTranslator;

    public function __construct(string $source, ParserInterface $parser, AstTranslatorInterface $astTranslator)
    {
        $this->source = $source;
        $this->parser = $parser;
        $this->astTranslator = $astTranslator;
    }

    public function __invoke(NodeValueInterface $rootNode, RuntimeInterface $runtime): ValueListInterface
    {
        return $this->getLoadedQuery()($rootNode, $runtime);
    }

    public function getCapabilities(): CapabilitiesInterface
    {
        return $this
            ->getLoadedQuery()
            ->getCapabilities();
    }

    public function getSource(): string
    {
        return $this->source;
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
            ->buildQueryAst($this->source);

        return $this
            ->astTranslator
            ->buildQuery($this->source, $queryAst);
    }
}
