<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Path;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Path\Path
 */
class PathTest extends TestCase
{

    public function testGetElements_ConstructedWithGivenElements_ReturnsSameValues(): void
    {
        $path = new Path('a', 1);
        self::assertSame(['a', 1], $path->getElements());
    }

    public function testCopyWithElement_ConstructedInstance_ReturnsAnotherInstance(): void
    {
        $path = new Path();
        self::assertNotSame($path, $path->copyWithElement(1));
    }

    public function testCopyWithElement_Constructed_ResultContainsMatchingElements(): void
    {
        $path = new Path('a');
        self::assertSame(['a', 1], $path->copyWithElement(1)->getElements());
    }

    public function testCopyWithProperty_ConstructedInstance_ReturnsAnotherInstance(): void
    {
        $path = new Path();
        self::assertNotSame($path, $path->copyWithProperty('a'));
    }

    public function testCopyWithProperty_Constructed_ResultContainsMatchingElements(): void
    {
        $path = new Path('a');
        self::assertSame(['a', 'b'], $path->copyWithProperty('b')->getElements());
    }

    /**
     * @param array $firstElements
     * @param array $secondElements
     * @dataProvider providerEquals
     */
    public function testEquals_EqualPath_ReturnsTrue(array $firstElements, array $secondElements): void
    {
        $firstPath = new Path(...$firstElements);
        $secondPath = new Path(...$secondElements);

        self::assertTrue($firstPath->equals($secondPath));
    }

    public function providerEquals(): array
    {
        return [
            'Empty paths' => [[], []],
            'Paths with same elements sequence' => [['a', 1], ['a', 1]],
        ];
    }

    /**
     * @param array $firstElements
     * @param array $secondElements
     * @dataProvider providerNotEquals
     */
    public function testEquals_NotEqualPath_ReturnsFalse(array $firstElements, array $secondElements): void
    {
        $firstPath = new Path(...$firstElements);
        $secondPath = new Path(...$secondElements);

        self::assertFalse($firstPath->equals($secondPath));
    }

    public function providerNotEquals(): array
    {
        return [
            'Nested paths' => [['a', 1], ['a']],
            'Paths with different element sequences' => [['a', 1], [1, 'a']],
        ];
    }
}
