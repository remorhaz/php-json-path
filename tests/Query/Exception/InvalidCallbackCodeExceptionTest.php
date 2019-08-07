<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\InvalidCallbackCodeException;

/**
 * @covers \Remorhaz\JSON\Path\Query\Exception\InvalidCallbackCodeException
 */
class InvalidCallbackCodeExceptionTest extends TestCase
{

    public function testGetCallbackCode_ConstructedWithGivenCallbackCode_ReturnsSameValue(): void
    {
        $exception = new InvalidCallbackCodeException('a');
        self::assertSame('a', $exception->getCallbackCode());
    }

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidCallbackCodeException('a');
        self::assertSame("Invalid query callback code generated:\n\na", $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidCallbackCodeException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidCallbackCodeException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new InvalidCallbackCodeException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
