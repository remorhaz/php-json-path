<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class LazyQuery implements QueryInterface
{
    private ?QueryInterface $loadedQuery = null;

    public function __construct(
        private readonly string $source,
        private readonly ParserInterface $parser,
        private readonly AstTranslatorInterface $astTranslator,
        private readonly CallbackBuilderInterface $callbackBuilder,
    ) {
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
        return $this->loadedQuery ??= $this->loadQuery();
    }

    private function loadQuery(): QueryInterface
    {
        $queryAst = $this
            ->parser
            ->buildQueryAst($this->source);

        return $this
            ->astTranslator
            ->buildQuery($this->source, $queryAst, $this->callbackBuilder);
    }
}
