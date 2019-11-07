<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Exception\InvalidPathElementException;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Exception\InvalidPathElementException
 */
class InvalidPathElementExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new InvalidPathElementException(1);
        self::assertRegExp('/^Invalid path element type: /', $exception->getMessage());
    }

    public function testGetPathElement_ConstructedWithPathElement_ReturnsSameValue(): void
    {
        $exception = new InvalidPathElementException('a');
        self::assertSame('a', $exception->getPathElement());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new InvalidPathElementException(1);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidPathElementException(1);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new InvalidPathElementException(1, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
