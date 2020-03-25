<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Matcher\Exception\AddressNotSortableException;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Matcher\Exception\AddressNotSortableException
 */
class AddressNotSortableExceptionTest extends TestCase
{

    public function testGetMessage_ConstructedWithAddress_ReturnsMatchingValue(): void
    {
        $exception = new AddressNotSortableException(1);
        self::assertSame('Index/property is not sortable: 1', $exception->getMessage());
    }

    /**
     * @param $address
     * @param $expectedValue
     * @dataProvider providerGetAddress
     */
    public function testGetAddress_ConstructedWithAddress_ReturnsSameValue($address, $expectedValue): void
    {
        $exception = new AddressNotSortableException($address);
        self::assertSame($expectedValue, $exception->getAddress());
    }

    public function providerGetAddress(): array
    {
        return [
            'String value' => ['a', 'a'],
            'Integer value' => [1, 1],
        ];
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new AddressNotSortableException(1);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new AddressNotSortableException(1);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new AddressNotSortableException(1, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
