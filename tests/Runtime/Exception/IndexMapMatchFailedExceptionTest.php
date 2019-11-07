<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Exception\IndexMapMatchFailedException;
use Remorhaz\JSON\Path\Value\ValueListInterface;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Exception\IndexMapMatchFailedException
 */
class IndexMapMatchFailedExceptionTest extends TestCase
{

    public function testGetLeftValues_ConstructedWithGivenLeftValues_ReturnsSameInstance(): void
    {
        $leftValues = $this->createMock(ValueListInterface::class);
        $exception = new IndexMapMatchFailedException(
            $leftValues,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame($leftValues, $exception->getLeftValues());
    }

    public function testGetRightValues_ConstructedWithGivenRightValues_ReturnsSameInstance(): void
    {
        $rightValues = $this->createMock(ValueListInterface::class);
        $exception = new IndexMapMatchFailedException(
            $this->createMock(ValueListInterface::class),
            $rightValues
        );
        self::assertSame($rightValues, $exception->getRightValues());
    }

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new IndexMapMatchFailedException(
            $this->createMock(ValueListInterface::class),
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame('Index map match failed', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new IndexMapMatchFailedException(
            $this->createMock(ValueListInterface::class),
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new IndexMapMatchFailedException(
            $this->createMock(ValueListInterface::class),
            $this->createMock(ValueListInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new IndexMapMatchFailedException(
            $this->createMock(ValueListInterface::class),
            $this->createMock(ValueListInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
