<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\QueryCallbackNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Query\Exception\QueryCallbackNotFoundException
 */
class QueryCallbackNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryCallbackNotFoundException;
        self::assertSame('Query callback function is accessed before construction', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new QueryCallbackNotFoundException;
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryCallbackNotFoundException;
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new QueryCallbackNotFoundException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
