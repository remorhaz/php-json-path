<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\InvalidScalarDataException;

/**
 * @covers \Remorhaz\JSON\Path\Value\Exception\InvalidScalarDataException
 */
class InvalidScalarDataExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidScalarDataException(null);
        self::assertSame('Invalid scalar data', $exception->getMessage());
    }

    public function testGetData_ConstructedWithGivenData_ReturnsSameValue(): void
    {
        $exception = new InvalidScalarDataException([1, 2]);
        self::assertSame([1, 2], $exception->getData());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidScalarDataException(null);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidScalarDataException(null);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new InvalidScalarDataException(null, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
