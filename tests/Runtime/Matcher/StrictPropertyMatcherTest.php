<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\StrictPropertyMatcher;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Matcher\StrictPropertyMatcher
 */
class StrictPropertyMatcherTest extends TestCase
{
    public function testMatch_ConstructedWithGivenAddressInProperties_ReturnsTrue(): void
    {
        $matcher = new StrictPropertyMatcher('a');
        $actualValue = $matcher->match(
            'a',
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($actualValue);
    }

    /**
     * @param array $properties
     * @param       $address
     * @dataProvider providerNoAddressAmongProperties
     */
    public function testMatch_ConstructedWithoutGivenAddressInProperties_ReturnsFalse(array $properties, $address): void
    {
        $matcher = new StrictPropertyMatcher(...$properties);
        $actualValue = $matcher->match(
            $address,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($actualValue);
    }

    public function providerNoAddressAmongProperties(): array
    {
        return [
            'Property not listed' => [[], 'a'],
            'Listed property as integer' => [['1'], 1],
        ];
    }
}
