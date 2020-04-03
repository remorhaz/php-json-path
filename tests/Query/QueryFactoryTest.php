<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
use Remorhaz\JSON\Path\Query\LazyQuery;
use Remorhaz\JSON\Path\Query\AstTranslatorInterface;
use Remorhaz\JSON\Path\Query\QueryFactory;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\UniLex\AST\Tree;

/**
 * @covers \Remorhaz\JSON\Path\Query\QueryFactory
 */
class QueryFactoryTest extends TestCase
{

    public function testCreate_Always_ReturnsQueryFactoryInstance(): void
    {
        self::assertInstanceOf(QueryFactory::class, QueryFactory::create());
    }

    public function testCreateQuery_Constructed_ReturnsLazyQueryInstance(): void
    {
        $factory = new QueryFactory(
            $this->createMock(ParserInterface::class),
            $this->createMock(AstTranslatorInterface::class)
        );

        $actualValue = $factory->createQuery('a');
        self::assertInstanceOf(LazyQuery::class, $actualValue);
    }

    public function testCreateQuery_AstTranslatorReturnsQuery_ResultInvocationInvokesSameInstance(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $astTranslator
            ->method('buildQuery')
            ->willReturn($query);
        $factory = new QueryFactory(
            $this->createMock(ParserInterface::class),
            $astTranslator
        );
        $lazyQuery = $factory->createQuery('a');

        $rootValue = $this->createMock(NodeValueInterface::class);
        $runtime = $this->createMock(RuntimeInterface::class);
        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with($rootValue, $runtime);
        $lazyQuery($rootValue, $runtime);
    }

    public function testCreateQuery_ConstructedWithPath_OnResultInvocationParserAcceptSamePath(): void
    {
        $parser = $this->createMock(ParserInterface::class);

        $factory = new QueryFactory(
            $parser,
            $this->createMock(AstTranslatorInterface::class)
        );
        $lazyQuery = $factory->createQuery('a');

        $parser
            ->expects(self::once())
            ->method('buildQueryAst')
            ->with('a');
        $lazyQuery(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(RuntimeInterface::class),
        );
    }

    public function testCreateQuery_ConstructedWithParser_OnResultInvocationParserPassesAstToTranslator(): void
    {
        $ast = new Tree();
        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->method('buildQueryAst')
            ->willReturn($ast);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $factory = new QueryFactory($parser, $astTranslator);
        $lazyQuery = $factory->createQuery('a');

        $astTranslator
            ->expects(self::once())
            ->method('buildQuery')
            ->with('a', $ast);
        $lazyQuery(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(RuntimeInterface::class),
        );
    }
}
