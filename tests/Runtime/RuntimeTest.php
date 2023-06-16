<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Runtime\LiteralFactoryInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\MatcherFactoryInterface;
use Remorhaz\JSON\Path\Runtime\Runtime;
use Remorhaz\JSON\Path\Runtime\ValueListFetcherInterface;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Runtime
 */
class RuntimeTest extends TestCase
{
    public function testGetEvaluator_ConstructedWithGivenEvaluator_ReturnsSameInstance(): void
    {
        $evaluator = $this->createMock(EvaluatorInterface::class);
        $runtime = new Runtime(
            $this->createMock(ValueListFetcherInterface::class),
            $evaluator,
            $this->createMock(LiteralFactoryInterface::class),
            $this->createMock(MatcherFactoryInterface::class),
        );

        self::assertSame($evaluator, $runtime->getEvaluator());
    }

    public function testGetValueListFetcher_ConstructedWithGivenValueListFetcher_ReturnsSameInstance(): void
    {
        $valueListFetcher = $this->createMock(ValueListFetcherInterface::class);
        $runtime = new Runtime(
            $valueListFetcher,
            $this->createMock(EvaluatorInterface::class),
            $this->createMock(LiteralFactoryInterface::class),
            $this->createMock(MatcherFactoryInterface::class),
        );

        self::assertSame($valueListFetcher, $runtime->getValueListFetcher());
    }

    public function testGetLiteralFactory_ConstructedWithGivenLiteralFactory_ReturnsSameInstance(): void
    {
        $literalFactory = $this->createMock(LiteralFactoryInterface::class);
        $runtime = new Runtime(
            $this->createMock(ValueListFetcherInterface::class),
            $this->createMock(EvaluatorInterface::class),
            $literalFactory,
            $this->createMock(MatcherFactoryInterface::class),
        );

        self::assertSame($literalFactory, $runtime->getLiteralFactory());
    }

    public function testGetMatcherFactory_ConstructedWithGivenMatcherFactory_ReturnsSameInstance(): void
    {
        $matcherFactory = $this->createMock(MatcherFactoryInterface::class);
        $runtime = new Runtime(
            $this->createMock(ValueListFetcherInterface::class),
            $this->createMock(EvaluatorInterface::class),
            $this->createMock(LiteralFactoryInterface::class),
            $matcherFactory,
        );

        self::assertSame($matcherFactory, $runtime->getMatcherFactory());
    }
}
