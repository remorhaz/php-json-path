<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\CapabilitiesNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Query\Exception\CapabilitiesNotFoundException
 */
class PropertiesNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new CapabilitiesNotFoundException();
        self::assertSame('Properties are accessed before being set', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new CapabilitiesNotFoundException();
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new CapabilitiesNotFoundException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new CapabilitiesNotFoundException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
