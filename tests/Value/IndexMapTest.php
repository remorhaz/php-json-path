<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\OuterIndexNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMap;

#[CoversClass(IndexMap::class)]
class IndexMapTest extends TestCase
{
    /**
     * @param list<int|null> $outerIndexes
     * @param list<int|null> $expectedValue
     */
    #[DataProvider('providerGetOuterIndexes')]
    public function testGetOuterIndexes_ConstructedWithOuterIndexes_ReturnsSameValuesInArray(
        array $outerIndexes,
        array $expectedValue,
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->getOuterIndexes());
    }

    /**
     * @return iterable<string, array{int|null, int|null}>
     */
    public static function providerGetOuterIndexes(): iterable
    {
        return [
            'Empty map' => [[], []],
            'Single null index' => [[null], [null]],
            'Single integer index' => [[1], [1]],
            'Two indexes' => [[1, 2], [1, 2]],
        ];
    }

    /**
     * @param list<int|null> $outerIndexes
     * @param int            $expectedValue
     */
    #[DataProvider('providerCount')]
    public function testCount_ConstructedWithOuterIndexes_ReturnsMatchingValue(
        array $outerIndexes,
        int $expectedValue,
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertCount($expectedValue, $map);
    }

    /**
     * @return iterable<string, array{list<int|null>, int}>
     */
    public static function providerCount(): iterable
    {
        return [
            'Empty map' => [[], 0],
            'Single null index' => [[null], 1],
            'Single integer index' => [[1], 1],
            'Two indexes' => [[1, 2], 2],
        ];
    }

    /**
     * @param list<int|null> $outerIndexes
     * @param list<int|null> $expectedValue
     */
    #[DataProvider('providerGetInnerIndexes')]
    public function testGetInnerIndexes_ConstructedWithOuterIndexes_ReturnsMatchingArray(
        array $outerIndexes,
        array $expectedValue,
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->getInnerIndexes());
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null>}>
     */
    public static function providerGetInnerIndexes(): iterable
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
     * @param list<int|null> $outerIndexes
     * @param list<int|null> $expectedValue
     */
    #[DataProvider('providerSplit')]
    public function testSplit_ConstructedWithOuterIndexes_ReturnsMatchingMap(
        array $outerIndexes,
        array $expectedValue,
    ): void {
        $map = new IndexMap(...$outerIndexes);
        self::assertSame($expectedValue, $map->split()->getOuterIndexes());
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null, int|null>}>
     */
    public static function providerSplit(): iterable
    {
        return [
            'Empty map' => [[], []],
            'Single null index' => [[null], [0]],
            'Single integer index' => [[1], [0]],
            'Two indexes' => [[1, 2], [0, 1]],
        ];
    }

    /**
     * @param list<int|null> $mapOuterIndexes
     * @param list<int|null> $argOuterIndexes
     * @param list<int|null> $expectedValue
     */
    #[DataProvider('providerJoin')]
    public function testJoin_GivenAnotherMap_ReturnsMatchingMap(
        array $mapOuterIndexes,
        array $argOuterIndexes,
        array $expectedValue,
    ): void {
        $map = new IndexMap(...$mapOuterIndexes);
        $actualValue = $map
            ->join(new IndexMap(...$argOuterIndexes))
            ->getOuterIndexes();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null>, list<int|null>}>
     */
    public static function providerJoin(): iterable
    {
        return [
            'Both maps are empty' => [[], [], []],
            'Maps contain same indexes' => [[0, 1], [0, 1], [0, 1]],
            'Argument contains more indexes than map' => [[0], [0, 1], [0, null]],
            'Argument contains part of map indexes' => [[0, 1], [0], [0]],
        ];
    }

    /**
     * @param list<int|null> $mapOuterIndexes
     * @param list<int|null> $argOuterIndexes
     */
    #[DataProvider('providerEqualMaps')]
    public function testEquals_EqualMaps_ReturnsTrue(
        array $mapOuterIndexes,
        array $argOuterIndexes,
    ): void {
        $map = new IndexMap(...$mapOuterIndexes);
        $actualValue = $map->equals(new IndexMap(...$argOuterIndexes));
        self::assertTrue($actualValue);
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null>}>
     */
    public static function providerEqualMaps(): iterable
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
     * @param list<int|null> $firstOuterIndexes
     * @param list<int|null> $secondOuterIndexes
     */
    #[DataProvider('providerIncompatibleMaps')]
    public function testIsCompatible_IncompatibleMaps_ReturnsFalse(
        array $firstOuterIndexes,
        array $secondOuterIndexes,
    ): void {
        $firstMap = new IndexMap(...$firstOuterIndexes);
        $secondMap = new IndexMap(...$secondOuterIndexes);
        self::assertFalse($firstMap->isCompatible($secondMap));
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null>}>
     */
    public static function providerIncompatibleMaps(): iterable
    {
        return [
            'Different map sizes' => [[1], [1, 2]],
            'Different outer indexes' => [[1], [2]],
        ];
    }

    /**
     * @param list<int|null> $firstOuterIndexes
     * @param list<int|null> $secondOuterIndexes
     */
    #[DataProvider('providerCompatibleMaps')]
    public function testIsCompatible_CompatibleMaps_ReturnsTrue(
        array $firstOuterIndexes,
        array $secondOuterIndexes,
    ): void {
        $firstMap = new IndexMap(...$firstOuterIndexes);
        $secondMap = new IndexMap(...$secondOuterIndexes);
        self::assertTrue($firstMap->isCompatible($secondMap));
    }

    /**
     * @return iterable<string, array{list<int|null>, list<int|null>}>
     */
    public static function providerCompatibleMaps(): iterable
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
