<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Parser;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Parser\Exception\QueryAstNotBuiltException;
use Remorhaz\JSON\Path\Parser\Ll1ParserFactoryInterface;
use Remorhaz\JSON\Path\Parser\Parser;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;

/**
 * @covers \Remorhaz\JSON\Path\Parser\Parser
 */
class ParserTest extends TestCase
{

    public function testBuildQueryAst_Constructed_ReturnsTreeInstancePassedToLl1ParserFactory(): void
    {
        $ll1ParserFactory = $this->createMock(Ll1ParserFactoryInterface::class);
        $queryAst = null;
        $onQueryAst = function (Tree $passedQueryAst) use (&$queryAst): bool {
            $queryAst = $passedQueryAst;

            return true;
        };
        $ll1ParserFactory
            ->method('createParser')
            ->with(self::anything(), self::callback($onQueryAst));

        $parser = new Parser($ll1ParserFactory);
        $actualValue = $parser->buildQueryAst('a');
        self::assertSame($queryAst, $actualValue);
    }

    public function testBuildQueryAst_GivenPath_PassesSameValueToLl1ParserFactory(): void
    {
        $ll1ParserFactory = $this->createMock(Ll1ParserFactoryInterface::class);
        $parser = new Parser($ll1ParserFactory);

        $ll1ParserFactory
            ->expects(self::once())
            ->method('createParser')
            ->with('a', self::anything());
        $parser->buildQueryAst('a');
    }

    public function testBuildQueryAst_Ll1ParserThrowsExceptionOnRun_ThrowsException(): void
    {
        $ll1ParserFactory = $this->createMock(Ll1ParserFactoryInterface::class);
        $ll1Parser = $this->createMock(Ll1Parser::class);
        $ll1ParserFactory
            ->method('createParser')
            ->willReturn($ll1Parser);
        $ll1Parser
            ->method('run')
            ->willThrowException(new Exception);
        $parser = new Parser($ll1ParserFactory);

        $this->expectException(QueryAstNotBuiltException::class);
        $parser->buildQueryAst('a');
    }
}
