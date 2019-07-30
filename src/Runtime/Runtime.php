<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

final class Runtime implements RuntimeInterface
{

    private $valueListFetcher;

    private $evaluator;

    private $literalFactory;

    private $matcherFactory;

    public function __construct(
        ValueListFetcherInterface $valueListFetcher,
        EvaluatorInterface $evaluator,
        LiteralFactoryInterface $literalFactory,
        Matcher\MatcherFactoryInterface $matcherFactory
    ) {
        $this->valueListFetcher = $valueListFetcher;
        $this->evaluator = $evaluator;
        $this->literalFactory = $literalFactory;
        $this->matcherFactory = $matcherFactory;
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
