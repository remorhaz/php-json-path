<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Parser\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Parser\Exception\ParserCreationFailedException;

/**
 * @covers \Remorhaz\JSON\Path\Parser\Exception\ParserCreationFailedException
 */
class ParserCreationFailedExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ParserCreationFailedException();
        self::assertSame('Failed to create JSONPath parser', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ParserCreationFailedException();
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ParserCreationFailedException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ParserCreationFailedException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
