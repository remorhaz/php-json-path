<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Parser\ParserInterface;
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

    public function testInvoke_AstTranslatorReturnsQuery_InvokesSameInstance(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $astTranslator
            ->method('buildQuery')
            ->willReturn($query);

        $lazyQuery = new LazyQuery(
            'a',
            $this->createMock(ParserInterface::class),
            $astTranslator
        );

        $runtime = $this->createMock(RuntimeInterface::class);
        $rootNode = $this->createMock(NodeValueInterface::class);
        $query
            ->expects(self::once())
            ->method('__invoke')
            ->with($runtime, $rootNode);
        $lazyQuery($runtime, $rootNode);
    }

    public function testInvoke_ConstructedWithPath_PassesSamePathToParser(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $this->createMock(AstTranslatorInterface::class)
        );

        $parser
            ->expects(self::once())
            ->method('buildQueryAst')
            ->with('a');
        $lazyQuery(
            $this->createMock(RuntimeInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testInvoke_ParserReturnsAst_PassesSameAstToTranslator(): void
    {
        $tree = new Tree;
        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->method('buildQueryAst')
            ->willReturn($tree);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $lazyQuery = new LazyQuery('a', $parser, $astTranslator);

        $astTranslator
            ->expects(self::once())
            ->method('buildQuery')
            ->with('a', $tree);
        $lazyQuery(
            $this->createMock(RuntimeInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
    }

    public function testGetProperties_ConstructedWithPath_PassesSamePathToParser(): void
    {
        $parser = $this->createMock(ParserInterface::class);
        $lazyQuery = new LazyQuery(
            'a',
            $parser,
            $this->createMock(AstTranslatorInterface::class)
        );

        $parser
            ->expects(self::once())
            ->method('buildQueryAst')
            ->with('a');
        $lazyQuery->getCapabilities();
    }


    public function testGetCapabilities_ParserReturnsAst_PassesSameAstToTranslator(): void
    {
        $tree = new Tree;
        $parser = $this->createMock(ParserInterface::class);
        $parser
            ->method('buildQueryAst')
            ->willReturn($tree);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $lazyQuery = new LazyQuery('a', $parser, $astTranslator);

        $astTranslator
            ->expects(self::once())
            ->method('buildQuery')
            ->with('a', $tree);
        $lazyQuery->getCapabilities();
    }


    public function testGetCapabilities_AstTranslatorReturnsQueryWithGivenProperties_ReturnsSameInstance(): void
    {
        $properties = new Capabilities(false, false);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($properties);
        $astTranslator = $this->createMock(AstTranslatorInterface::class);
        $astTranslator
            ->method('buildQuery')
            ->willReturn($query);

        $lazyQuery = new LazyQuery(
            'a',
            $this->createMock(ParserInterface::class),
            $astTranslator
        );

        self::assertSame($properties, $lazyQuery->getCapabilities());
    }
}
