<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Processor\Result\Exception\PathNotFoundInValueException;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\Exception\PathNotFoundInValueException
 */
class PathNotFoundInValueExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new PathNotFoundInValueException(
            $this->createMock(ValueInterface::class)
        );
        self::assertSame('Path not found in value', $exception->getMessage());
    }

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ValueInterface::class);
        $exception = new PathNotFoundInValueException($value);
        self::assertSame($value, $exception->getValue());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new PathNotFoundInValueException(
            $this->createMock(ValueInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new PathNotFoundInValueException(
            $this->createMock(ValueInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new PathNotFoundInValueException(
            $this->createMock(ValueInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
