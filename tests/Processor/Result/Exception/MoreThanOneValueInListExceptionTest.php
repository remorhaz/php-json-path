<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Result\Exception\MoreThanOneValueInListException;
use Remorhaz\JSON\Path\Value\ValueListInterface;

#[CoversClass(MoreThanOneValueInListException::class)]
class MoreThanOneValueInListExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new MoreThanOneValueInListException(
            $this->createMock(ValueListInterface::class),
        );

        self::assertSame('More than 1 value in list', $exception->getMessage());
    }

    public function testGetValues_ConstructedWithValues_ReturnsSameInstance(): void
    {
        $values = $this->createMock(ValueListInterface::class);
        $exception = new MoreThanOneValueInListException($values);
        self::assertSame($values, $exception->getValues());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new MoreThanOneValueInListException(
            $this->createMock(ValueListInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new MoreThanOneValueInListException(
            $this->createMock(ValueListInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
