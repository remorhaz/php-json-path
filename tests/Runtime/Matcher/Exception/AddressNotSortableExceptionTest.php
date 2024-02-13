<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Matcher\Exception\AddressNotSortableException;

#[CoversClass(AddressNotSortableException::class)]
class AddressNotSortableExceptionTest extends TestCase
{
    public function testGetMessage_ConstructedWithAddress_ReturnsMatchingValue(): void
    {
        $exception = new AddressNotSortableException(1);
        self::assertSame('Index/property is not sortable: 1', $exception->getMessage());
    }

    #[DataProvider('providerGetAddress')]
    public function testGetAddress_ConstructedWithAddress_ReturnsSameValue(
        int|string $address,
        int|string $expectedValue,
    ): void {
        $exception = new AddressNotSortableException($address);
        self::assertSame($expectedValue, $exception->getAddress());
    }

    /**
     * @return iterable<string, array{int|string, int|string}>
     */
    public static function providerGetAddress(): iterable
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
