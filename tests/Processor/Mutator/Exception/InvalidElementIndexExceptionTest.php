<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Mutator\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Mutator\Exception\InvalidElementIndexException;

#[CoversClass(InvalidElementIndexException::class)]
class InvalidElementIndexExceptionTest extends TestCase
{
    public function testGetMessage_ConstructedWithString_ReturnsMatchingResult(): void
    {
        $exception = new InvalidElementIndexException('a');
        self::assertSame(
            'Element index is not an integer: \'a\'',
            $exception->getMessage(),
        );
    }

    public function testGetMessage_ConstructedWithNull_ReturnsMatchingResult(): void
    {
        $exception = new InvalidElementIndexException(null);
        self::assertSame(
            'Element index is not an integer: NULL',
            $exception->getMessage(),
        );
    }

    public function testGetIndex_Constructed_ReturnsMatchingResult(): void
    {
        $exception = new InvalidElementIndexException('a');
        self::assertSame('a', $exception->getIndex());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidElementIndexException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidElementIndexException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
