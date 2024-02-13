<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\QueryAstNotTranslatedException;
use Remorhaz\UniLex\AST\Tree;

#[CoversClass(QueryAstNotTranslatedException::class)]
class QueryAstNotTranslatedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryAstNotTranslatedException(new Tree());
        self::assertSame('Query AST was not translated to callback function', $exception->getMessage());
    }

    public function testGetQueryAst_ConstructedWithGivenQueryAst_ReturnsSameInstance(): void
    {
        $tree = new Tree();
        $exception = new QueryAstNotTranslatedException($tree);
        self::assertSame($tree, $exception->getQueryAst());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryAstNotTranslatedException(new Tree());
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new QueryAstNotTranslatedException(new Tree(), $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
