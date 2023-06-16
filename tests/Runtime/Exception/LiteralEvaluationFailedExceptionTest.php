<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Exception\LiteralEvaluationFailedException;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Exception\LiteralEvaluationFailedException
 */
class LiteralEvaluationFailedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new LiteralEvaluationFailedException(
            $this->createMock(LiteralValueInterface::class)
        );
        self::assertSame('Failed to evaluate literal value', $exception->getMessage());
    }

    public function testGetLiteral_ConstructedWithLiteral_ReturnsSameInstance(): void
    {
        $value = $this->createMock(LiteralValueInterface::class);
        $exception = new LiteralEvaluationFailedException($value);
        self::assertSame($value, $exception->getLiteral());
    }

    public function testGetCode_Always_ReturnZero(): void
    {
        $exception = new LiteralEvaluationFailedException(
            $this->createMock(LiteralValueInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new LiteralEvaluationFailedException(
            $this->createMock(LiteralValueInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new LiteralEvaluationFailedException(
            $this->createMock(LiteralValueInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
