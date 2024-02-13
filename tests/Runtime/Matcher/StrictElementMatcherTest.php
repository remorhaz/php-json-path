<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\StrictElementMatcher;

#[CoversClass(StrictElementMatcher::class)]
class StrictElementMatcherTest extends TestCase
{
    public function testMatch_ConstructedWithGivenAddressInIndexes_ReturnsTrue(): void
    {
        $matcher = new StrictElementMatcher(1);
        $actualValue = $matcher->match(
            1,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class),
        );
        self::assertTrue($actualValue);
    }

    /**
     * @param list<int>  $indexes
     * @param int|string $address
     */
    #[DataProvider('providerNoAddressAmongIndexes')]
    public function testMatch_ConstructedWithoutGivenAddressInIndexes_ReturnsFalse(
        array $indexes,
        int|string $address,
    ): void {
        $matcher = new StrictElementMatcher(...$indexes);
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
    public static function providerNoAddressAmongIndexes(): iterable
    {
        return [
            'Index not listed' => [[], 1],
            'Listed index as string' => [[1], '1'],
        ];
    }
}
