<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Parser\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Parser\Exception\QueryAstNotBuiltException;

/**
 * @covers \Remorhaz\JSON\Path\Parser\Exception\QueryAstNotBuiltException
 */
class QueryAstNotBuiltExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryAstNotBuiltException('a');
        self::assertSame('Failed to build AST from JSONPath query: a', $exception->getMessage());
    }

    public function testGetSource_ConstructedWithGivenSource_ReturnsMatchingValue(): void
    {
        $exception = new QueryAstNotBuiltException('a');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new QueryAstNotBuiltException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryAstNotBuiltException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new QueryAstNotBuiltException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
