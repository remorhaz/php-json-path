<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Capabilities;

/**
 * @covers \Remorhaz\JSON\Path\Query\Capabilities
 */
class CapabilitiesTest extends TestCase
{
    /**
     * @param bool $isDefinite
     * @param bool $expectedValue
     * @dataProvider providerIsDefinite
     */
    public function testIsDefinite_ConstructedWithIsDefiniteFlag_ReturnsSameValue(
        bool $isDefinite,
        bool $expectedValue
    ): void {
        $properties = new Capabilities($isDefinite, false);
        self::assertSame($expectedValue, $properties->isDefinite());
    }

    public function providerIsDefinite(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }

    /**
     * @param bool $isPath
     * @param bool $expectedValue
     * @dataProvider providerIsAddressable
     */
    public function testIsAddressable_ConstructedWithIsPathFlag_ReturnsSameValue(
        bool $isPath,
        bool $expectedValue
    ): void {
        $properties = new Capabilities(false, $isPath);
        self::assertSame($expectedValue, $properties->isAddressable());
    }

    public function providerIsAddressable(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
