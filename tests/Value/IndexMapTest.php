<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\OuterIndexNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMap;

/**
 * @covers \Remorhaz\JSON\Path\Value\IndexMap
 */
class IndexMapTest extends TestCase
{

    /**
     * @param array $outerIndexes
     * @param array $expectedValue
     * @dataProvider providerGetOuterIndexes
     */
    public function testGetOuterIndexes_ConstructedWithOuterIndexes_ReturnsSameValuesInArray(
        array $outerIndexes,
        array $expectedValue
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->getOuterIndexes());
    }

    public function providerGetOuterIndexes(): array
    {
        return [
            'Empty map' => [[], []],
            'Single null index' => [[null], [null]],
            'Single integer index' => [[1], [1]],
            'Two indexes' => [[1, 2], [1, 2]],
        ];
    }

    /**
     * @param array $outerIndexes
     * @param int   $expectedValue
     * @dataProvider providerCount
     */
    public function testCount_ConstructedWithOuterIndexes_ReturnsMatchingValue(
        array $outerIndexes,
        int $expectedValue
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertCount($expectedValue, $map);
    }

    public function providerCount(): array
    {
        return [
            'Empty map' => [[], 0],
            'Single null index' => [[null], 1],
            'Single integer index' => [[1], 1],
            'Two indexes' => [[1, 2], 2],
        ];
    }

    /**
     * @param array $outerIndexes
     * @param array $expectedValue
     * @dataProvider providerGetInnerIndexes
     */
    public function testGetInnerIndexes_ConstructedWithOuterIndexes_ReturnsMatchingArray(
        array $outerIndexes,
        array $expectedValue
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->getInnerIndexes());
    }

    public function providerGetInnerIndexes(): array
    {
        return [
            'Empty map' => [[], []],
            'Single null index' => [[null], [0]],
            'Single integer index' => [[1], [0]],
            'Two indexes' => [[1, 2], [0, 1]],
        ];
    }

    public function testGetOuterIndex_IntegerOuterIndexExists_ReturnsMatchingValue(): void
    {
        $map = new IndexMap(1);
        self::assertSame(1, $map->getOuterIndex(0));
    }

    public function testGetOuterIndex_NullOuterIndexExists_ThrowsException(): void
    {
        $map = new IndexMap(null);
        $this->expectException(OuterIndexNotFoundException::class);
        $map->getOuterIndex(0);
    }

    public function testGetOuterIndex_InnerIndexNotExists_ThrowsException(): void
    {
        $map = new IndexMap();
        $this->expectException(OuterIndexNotFoundException::class);
        $map->getOuterIndex(0);
    }

    public function testOuterIndexExists_OuterIndexExists_ReturnsTrue(): void
    {
        $map = new IndexMap(1);
        self::assertTrue($map->outerIndexExists(1));
    }

    public function testOuterIndexExists_OuterIndexNotExists_ReturnsTrue(): void
    {
        $map = new IndexMap();
        self::assertFalse($map->outerIndexExists(1));
    }

    /**
     * @param int[] $outerIndexes
     * @param int[] $expectedValue
     * @dataProvider providerSplit
     */
    public function testSplit_ConstructedWithOuterIndexes_ReturnsMatchingMap(
        array $outerIndexes,
        array $expectedValue
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->split()->getOuterIndexes());
    }

    public function providerSplit(): array
    {
        return [
            'Empty map' => [[], []],
            'Single null index' => [[null], [0]],
            'Single integer index' => [[1], [0]],
            'Two indexes' => [[1, 2], [0, 1]],
        ];
    }

    /**
     * @param array $mapOuterIndexes
     * @param array $argOuterIndexes
     * @param array $expectedValue
     * @dataProvider providerJoin
     */
    public function testJoin_GivenAnotherMap_ReturnsMatchingMap(
        array $mapOuterIndexes,
        array $argOuterIndexes,
        array $expectedValue
    ): void {
        $map = new IndexMap(...$mapOuterIndexes);
        $actualValue = $map
            ->join(new IndexMap(...$argOuterIndexes))
            ->getOuterIndexes();
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerJoin(): array
    {
        return [
            'Both maps are empty' => [[], [], []],
            'Maps contain same indexes' => [[0, 1], [0, 1], [0, 1]],
            'Argument contains more indexes than map' => [[0], [0, 1], [0, null]],
            'Argument contains part of map indexes' => [[0, 1], [0], [0]],
        ];
    }

    /**
     * @param array $mapOuterIndexes
     * @param array $argOuterIndexes
     * @dataProvider providerEqualMaps
     */
    public function testEquals_EqualMaps_ReturnsTrue(
        array $mapOuterIndexes,
        array $argOuterIndexes
    ): void {
        $map = new IndexMap(...$mapOuterIndexes);
        $actualValue = $map->equals(new IndexMap(...$argOuterIndexes));
        self::assertTrue($actualValue);
    }

    public function providerEqualMaps(): array
    {
        return [
            'Empty maps' => [[], []],
            'Non-empty maps' => [[0, null], [0, null]],
        ];
    }

    public function testEquals_NotEqualMaps_ReturnsFalse(): void
    {
        $map = new IndexMap(1);
        $actualValue = $map->equals(new IndexMap(2));
        self::assertFalse($actualValue);
    }

    /**
     * @param array $firstOuterIndexes
     * @param array $secondOuterIndexes
     * @dataProvider providerIncompatibleMaps
     */
    public function testIsCompatible_IncompatibleMaps_ReturnsFalse(
        array $firstOuterIndexes,
        array $secondOuterIndexes
    ): void {
        $firstMap = new IndexMap(...$firstOuterIndexes);
        $secondMap = new IndexMap(...$secondOuterIndexes);
        self::assertFalse($firstMap->isCompatible($secondMap));
    }

    public function providerIncompatibleMaps(): array
    {
        return [
            'Different map sizes' => [[1], [1, 2]],
            'Different outer indexes' => [[1], [2]],
        ];
    }

    /**
     * @param array $firstOuterIndexes
     * @param array $secondOuterIndexes
     * @dataProvider providerCompatibleMaps
     */
    public function testIsCompatible_CompatibleMaps_ReturnsTrue(
        array $firstOuterIndexes,
        array $secondOuterIndexes
    ): void {
        $firstMap = new IndexMap(...$firstOuterIndexes);
        $secondMap = new IndexMap(...$secondOuterIndexes);
        self::assertTrue($firstMap->isCompatible($secondMap));
    }

    public function providerCompatibleMaps(): array
    {
        return [
            'Empty maps' => [[], []],
            'Both outer indexes are null' => [[null], [null]],
            'Left outer index is null' => [[null], [1]],
            'Right outer index is null' => [[1], [null]],
            'Same outer indexes after null left outer index' => [[null, 2], [1, 2]],
        ];
    }
}
