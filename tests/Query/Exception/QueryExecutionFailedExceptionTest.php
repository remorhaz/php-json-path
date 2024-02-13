<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\QueryExecutionFailedException;

#[CoversClass(QueryExecutionFailedException::class)]
class QueryExecutionFailedExceptionTest extends TestCase
{
    public function testGetSource_ConstructedWithGivenSource_ReturnsSameValue(): void
    {
        $exception = new QueryExecutionFailedException('a', 'b');
        self::assertSame('a', $exception->getSource());
    }

    public function testGetCallbackCode_ConstructedWithGivenCallbackCode_ReturnsSameValue(): void
    {
        $exception = new QueryExecutionFailedException('a', 'b');
        self::assertSame('b', $exception->getCallbackCode());
    }

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryExecutionFailedException('a', 'b');
        self::assertSame("Failed to execute JSONPath query: a\n\nb", $exception->getMessage());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryExecutionFailedException('a', 'b');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new QueryExecutionFailedException('a', 'b', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
