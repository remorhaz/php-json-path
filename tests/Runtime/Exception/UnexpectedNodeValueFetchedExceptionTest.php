<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Exception\UnexpectedNodeValueFetchedException;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Exception\UnexpectedNodeValueFetchedException
 */
class UnexpectedNodeValueFetchedExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new UnexpectedNodeValueFetchedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertSame('Unexpected node value fetched', $exception->getMessage());
    }

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $exception = new UnexpectedNodeValueFetchedException($value);
        self::assertSame($value, $exception->getValue());
    }

    public function testGetCode_Always_ReturnZero(): void
    {
        $exception = new UnexpectedNodeValueFetchedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new UnexpectedNodeValueFetchedException(
            $this->createMock(NodeValueInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new UnexpectedNodeValueFetchedException(
            $this->createMock(NodeValueInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
