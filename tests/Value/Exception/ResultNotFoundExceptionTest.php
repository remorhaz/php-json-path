<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\ResultNotFoundException;
use Remorhaz\JSON\Path\Value\ValueListInterface;

#[CoversClass(ResultNotFoundException::class)]
class ResultNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ResultNotFoundException(
            1,
            $this->createMock(ValueListInterface::class),
        );
        self::assertSame('Result not found in list at position 1', $exception->getMessage());
    }

    public function testGetIndex_ConstructedWithGivenIndex_ReturnsSameValue(): void
    {
        $exception = new ResultNotFoundException(
            1,
            $this->createMock(ValueListInterface::class),
        );
        self::assertSame(1, $exception->getIndex());
    }

    public function testGetValues_ConstructedWithGivenValues_ReturnsSameInstance(): void
    {
        $values = $this->createMock(ValueListInterface::class);
        $exception = new ResultNotFoundException(1, $values);
        self::assertSame($values, $exception->getValues());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ResultNotFoundException(
            1,
            $this->createMock(ValueListInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ResultNotFoundException(
            1,
            $this->createMock(ValueListInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
