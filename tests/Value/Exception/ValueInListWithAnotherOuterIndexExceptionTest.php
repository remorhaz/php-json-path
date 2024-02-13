<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\Exception\ValueInListWithAnotherOuterIndexException;

#[CoversClass(ValueInListWithAnotherOuterIndexException::class)]
class ValueInListWithAnotherOuterIndexExceptionTest extends TestCase
{
    public function testGetMessage_ConstructedWithIndexes_ReturnsMatchingValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2);
        self::assertSame('Value is already in list with outer index 1, not 2', $exception->getMessage());
    }

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2);
        self::assertSame($value, $exception->getValue());
    }

    public function testGetExpectedIndex_ConstructedWithExpectedIndex_ReturnsSameValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2);
        self::assertSame(1, $exception->getExpectedIndex());
    }

    public function testGetActualIndex_ConstructedWithActualIndex_ReturnsSameValue(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2);
        self::assertSame(2, $exception->getActualIndex());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new ValueInListWithAnotherOuterIndexException($value, 1, 2, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
