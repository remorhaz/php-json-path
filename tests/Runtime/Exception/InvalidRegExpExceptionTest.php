<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Exception\InvalidRegExpException;

#[CoversClass(InvalidRegExpException::class)]
class InvalidRegExpExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidRegExpException('a');
        self::assertSame('Error processing regular expression: a', $exception->getMessage());
    }

    public function testGetPattern_ConstructedWithPattern_ReturnsSameValue(): void
    {
        $exception = new InvalidRegExpException('a');
        self::assertSame('a', $exception->getPattern());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidRegExpException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidRegExpException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new InvalidRegExpException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
