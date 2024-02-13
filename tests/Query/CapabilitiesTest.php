<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Capabilities;

#[CoversClass(Capabilities::class)]
class CapabilitiesTest extends TestCase
{
    #[DataProvider('providerIsDefinite')]
    public function testIsDefinite_ConstructedWithIsDefiniteFlag_ReturnsSameValue(
        bool $isDefinite,
        bool $expectedValue,
    ): void {
        $properties = new Capabilities($isDefinite, false);
        self::assertSame($expectedValue, $properties->isDefinite());
    }

    /**
     * @return iterable<string, array{bool, bool}>
     */
    public static function providerIsDefinite(): iterable
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }

    #[DataProvider('providerIsAddressable')]
    public function testIsAddressable_ConstructedWithIsPathFlag_ReturnsSameValue(
        bool $isPath,
        bool $expectedValue,
    ): void {
        $properties = new Capabilities(false, $isPath);
        self::assertSame($expectedValue, $properties->isAddressable());
    }

    /**
     * @return iterable<string, array{bool, bool}>
     */
    public static function providerIsAddressable(): iterable
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
