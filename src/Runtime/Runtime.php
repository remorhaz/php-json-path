<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

final class Runtime implements RuntimeInterface
{
    public function __construct(
        private ValueListFetcherInterface $valueListFetcher,
        private EvaluatorInterface $evaluator,
        private LiteralFactoryInterface $literalFactory,
        private Matcher\MatcherFactoryInterface $matcherFactory,
    ) {
    }

    public function getEvaluator(): EvaluatorInterface
    {
        return $this->evaluator;
    }

    public function getLiteralFactory(): LiteralFactoryInterface
    {
        return $this->literalFactory;
    }

    public function getMatcherFactory(): Matcher\MatcherFactoryInterface
    {
        return $this->matcherFactory;
    }

    public function getValueListFetcher(): ValueListFetcherInterface
    {
        return $this->valueListFetcher;
    }
}
