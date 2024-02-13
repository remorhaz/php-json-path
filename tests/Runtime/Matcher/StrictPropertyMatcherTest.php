<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\StrictPropertyMatcher;

#[CoversClass(StrictPropertyMatcher::class)]
class StrictPropertyMatcherTest extends TestCase
{
    public function testMatch_ConstructedWithGivenAddressInProperties_ReturnsTrue(): void
    {
        $matcher = new StrictPropertyMatcher('a');
        $actualValue = $matcher->match(
            'a',
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class),
        );
        self::assertTrue($actualValue);
    }

    /**
     * @param list<string> $properties
     * @param int|string   $address
     */
    #[DataProvider('providerNoAddressAmongProperties')]
    public function testMatch_ConstructedWithoutGivenAddressInProperties_ReturnsFalse(array $properties, $address): void
    {
        $matcher = new StrictPropertyMatcher(...$properties);
        $actualValue = $matcher->match(
            $address,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class),
        );
        self::assertFalse($actualValue);
    }

    /**
     * @return iterable<string, array{list<int>, int|string}>
     */
    public static function providerNoAddressAmongProperties(): iterable
    {
        return [
            'Property not listed' => [[], 'a'],
            'Listed property as integer' => [['1'], 1],
        ];
    }
}
