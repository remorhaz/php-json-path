<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMapInterface;
use Remorhaz\JSON\Path\Value\LiteralArrayValueList;

/**
 * @covers \Remorhaz\JSON\Path\Value\LiteralArrayValueList
 */
class LiteralArrayValueListExceptionTest extends TestCase
{

    public function testGetIndexMap_ConstructedWithGivenIndexMap_ReturnsSameInstance(): void
    {
        $indexMap = $this->createMock(IndexMapInterface::class);
        $values = new LiteralArrayValueList($indexMap);
        self::assertSame($indexMap, $values->getIndexMap());
    }

    public function testGetValues_ConstructedWithoutValues_ReturnsEmptyArray(): void
    {
        $indexMap = $this->createMock(IndexMapInterface::class);
        $values = new LiteralArrayValueList($indexMap);
        self::assertSame([], $values->getValues());
    }

    public function testGetValues_ConstructedWithGivenValue_ReturnsArrayWithSameInstance(): void
    {
        $value = $this->createMock(ArrayValueInterface::class);
        $indexMap = $this->createMock(IndexMapInterface::class);
        $values = new LiteralArrayValueList($indexMap, $value);
        self::assertSame([$value], $values->getValues());
    }

    public function testGetValue_ConstructedWithValueAtGivenIndex_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ArrayValueInterface::class);
        $indexMap = $this->createMock(IndexMapInterface::class);
        $values = new LiteralArrayValueList($indexMap, $value);
        self::assertSame($value, $values->getValue(0));
    }

    public function testGetValue_ConstructedWithoutValueAtGivenIndex_ThrowsException(): void
    {
        $value = $this->createMock(ArrayValueInterface::class);
        $indexMap = $this->createMock(IndexMapInterface::class);

        $values = new LiteralArrayValueList($indexMap, $value);
        $this->expectException(ValueNotFoundException::class);
        $values->getValue(1);
    }
}
