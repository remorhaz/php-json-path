<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\StrictElementMatcher;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Matcher\StrictElementMatcher
 */
class StrictElementMatcherTest extends TestCase
{

    public function testMatch_ConstructedWithGivenAddressInIndexes_ReturnsTrue(): void
    {
        $matcher = new StrictElementMatcher(1);
        $actualValue = $matcher->match(
            1,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($actualValue);
    }

    /**
     * @param array $indexes
     * @param       $address
     * @dataProvider providerNoAddressAmongIndexes
     */
    public function testMatch_ConstructedWithoutGivenAddressInIndexes_ReturnsFalse(array $indexes, $address): void
    {
        $matcher = new StrictElementMatcher(...$indexes);
        $actualValue = $matcher->match(
            $address,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($actualValue);
    }

    public function providerNoAddressAmongIndexes(): array
    {
        return [
            'Index not listed' => [[], 1],
            'Listed index as string' => [[1], '1'],
        ];
    }
}
