<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
use Remorhaz\JSON\Path\Query\CallbackBuilderInterface;
use Remorhaz\JSON\Path\Query\Exception\ExceptionInterface;
use Remorhaz\JSON\Path\Query\LazyQuery;
use Remorhaz\JSON\Path\Query\AstTranslatorInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Query\Capabilities;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\UniLex\AST\Tree;

/**
 * @covers \Remorhaz\JSON\Path\Query\LazyQuery
 */
class LazyQueryTest extends TestCase
{

    /**
     * @throws ExceptionInterface
     */
    public function testInvoke_AstTranslatorReturnsQuery_InvokesSameInstance(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $astTranslator = $this->createStub(AstTranslatorInterface::class);
        $astTranslator
            ->method('buildQuery')
            ->willReturn($query);

        $lazyQuery = new LazyQuery(
            'a',
            $this->createStub(ParserInterface::class),
            $astTranslator,
            $this->createStub(CallbackBuilderInterface::class),
        );

        $rootNode = $this->createStub(NodeValueInterface::class);
        $runtime = $this->createStub(RuntimeInterface::class);
        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with(self::identicalTo($rootNode), self::identicalTo($runtime));
        $lazyQuery($rootNode, $runtime);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testInvoke_ConstructedWithPath_PassesSamePathToParser(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $this->createStub(AstTranslatorInterface::class),
            $this->createStub(CallbackBuilderInterface::class),
        );

        $parser
            ->expects(self::once())
            ->method('buildQueryAst')
            ->with(self::identicalTo('a'));
        $lazyQuery(
            $this->createStub(NodeValueInterface::class),
            $this->createStub(RuntimeInterface::class),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testInvoke_ParserReturnsAst_PassesSameAstToTranslator(): void
    {
        $tree = new Tree();
        $parser = $this->createStub(ParserInterface::class);
        $parser
            ->method('buildQueryAst')
            ->willReturn($tree);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $callbackBuilder = $this->createStub(CallbackBuilderInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $astTranslator,
            $callbackBuilder,
        );

        $astTranslator
            ->expects(self::once())
            ->method('buildQuery')
            ->with(
                self::identicalTo('a'),
                self::identicalTo($tree),
                self::identicalTo($callbackBuilder),
            );
        $lazyQuery(
            $this->createStub(NodeValueInterface::class),
            $this->createStub(RuntimeInterface::class),
        );
    }

    public function testGetProperties_ConstructedWithPath_PassesSamePathToParser(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $this->createStub(AstTranslatorInterface::class),
            $this->createStub(CallbackBuilderInterface::class),
        );

        $parser
            ->expects(self::once())
            ->method('buildQueryAst')
            ->with(self::identicalTo('a'));
        $lazyQuery->getCapabilities();
    }


    public function testGetCapabilities_ParserReturnsAst_PassesSameAstToTranslator(): void
    {
        $tree = new Tree();
        $parser = $this->createStub(ParserInterface::class);
        $parser
            ->method('buildQueryAst')
            ->willReturn($tree);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $astTranslator,
            $this->createStub(CallbackBuilderInterface::class),
        );

        $astTranslator
            ->expects(self::once())
            ->method('buildQuery')
            ->with('a', $tree);
        $lazyQuery->getCapabilities();
    }

    public function testGetCapabilities_AstTranslatorReturnsQueryWithGivenProperties_ReturnsSameInstance(): void
    {
        $properties = new Capabilities(false, false);
        $query = $this->createStub(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($properties);
        $astTranslator = $this->createStub(AstTranslatorInterface::class);
        $astTranslator
            ->method('buildQuery')
            ->willReturn($query);

        $lazyQuery = new LazyQuery(
            'a',
            $this->createStub(ParserInterface::class),
            $astTranslator,
            $this->createStub(CallbackBuilderInterface::class)
        );

        self::assertSame($properties, $lazyQuery->getCapabilities());
    }

    public function testGetSource_ConstructedWithGivenSource_ReturnsSameValue(): void
    {
        $lazyQuery = new LazyQuery(
            'a',
            $this->createStub(ParserInterface::class),
            $this->createStub(AstTranslatorInterface::class),
            $this->createStub(CallbackBuilderInterface::class),
        );
        self::assertSame('a', $lazyQuery->getSource());
    }
}
