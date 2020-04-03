<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException;
use Remorhaz\JSON\Path\Value\ValueListInterface;

/**
 * @covers \Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException
 */
class ValueNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ValueNotFoundException(
            1,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame('Value not found in list at position 1', $exception->getMessage());
    }

    public function testGetIndex_ConstructedWithGivenIndex_ReturnsSameValue(): void
    {
        $exception = new ValueNotFoundException(
            1,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetValues_ConstructedWithGivenValues_ReturnsSameInstance(): void
    {
        $values = $this->createMock(ValueListInterface::class);
        $exception = new ValueNotFoundException(1, $values);
        self::assertSame($values, $exception->getValues());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ValueNotFoundException(
            1,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithouitPrevious_ReturnsNull(): void
    {
        $exception = new ValueNotFoundException(
            1,
            $this->createMock(ValueListInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ValueNotFoundException(
            1,
            $this->createMock(ValueListInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
