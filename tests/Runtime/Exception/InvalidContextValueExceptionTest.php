<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Exception\InvalidContextValueException;

#[CoversClass(InvalidContextValueException::class)]
class InvalidContextValueExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidContextValueException(
            $this->createMock(ValueInterface::class),
        );
        self::assertSame('Invalid context value', $exception->getMessage());
    }

    public function testGetValue_ConstructedWithValue_ReturnsSameValue(): void
    {
        $value = $this->createMock(ValueInterface::class);
        $exception = new InvalidContextValueException($value);
        self::assertSame($value, $exception->getValue());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidContextValueException(
            $this->createMock(ValueInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidContextValueException(
            $this->createMock(ValueInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
