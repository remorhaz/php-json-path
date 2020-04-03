<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

interface RuntimeInterface
{

    public function getValueListFetcher(): ValueListFetcherInterface;

    public function getEvaluator(): EvaluatorInterface;

    public function getLiteralFactory(): LiteralFactoryInterface;

    public function getMatcherFactory(): Matcher\MatcherFactoryInterface;
}
