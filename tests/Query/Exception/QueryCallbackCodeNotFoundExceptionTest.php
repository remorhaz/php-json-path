<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\QueryCallbackCodeNotFoundException;

#[CoversClass(QueryCallbackCodeNotFoundException::class)]
class QueryCallbackCodeNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryCallbackCodeNotFoundException();
        self::assertSame('Query callback code is accessed before being generated', $exception->getMessage());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryCallbackCodeNotFoundException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new QueryCallbackCodeNotFoundException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
