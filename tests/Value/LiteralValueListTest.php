<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMap;
use Remorhaz\JSON\Path\Value\IndexMapInterface;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;
use Remorhaz\JSON\Path\Value\LiteralValueList;

/**
 * @covers \Remorhaz\JSON\Path\Value\LiteralValueList
 */
class LiteralValueListTest extends TestCase
{
    public function testGetIndexMap_ConstructedWithGivenIndexMap_ReturnsSameInstance(): void
    {
        $indexMap = $this->createMock(IndexMapInterface::class);
        $valueList = new LiteralValueList(
            $indexMap,
            $this->createMock(LiteralValueInterface::class)
        );
        self::assertSame($indexMap, $valueList->getIndexMap());
    }

    public function testGetLiteral_ConstructedWithGivenLiteral_ReturnsSameInstance(): void
    {
        $value = $this->createMock(LiteralValueInterface::class);
        $valueList = new LiteralValueList(
            $this->createMock(IndexMapInterface::class),
            $value
        );
        self::assertSame($value, $valueList->getLiteral());
    }

    public function testGetValues_IndexMapWithGivenIndexesAndGivenValue_ReturnsPopulatedValue(): void
    {
        $indexMap = new IndexMap(0, 0);
        $value = $this->createMock(LiteralValueInterface::class);
        $valueList = new LiteralValueList($indexMap, $value);
        self::assertSame([$value, $value], $valueList->getValues());
    }

    public function testGetValue_MapWithGivenInnerIndex_ReturnsValueInstance(): void
    {
        $indexMap = new IndexMap(0);
        $value = $this->createMock(LiteralValueInterface::class);
        $valueList = new LiteralValueList($indexMap, $value);
        self::assertSame($value, $valueList->getValue(0));
    }

    public function testGetValue_MapWithoutGivenInnerIndex_ThrowsException(): void
    {
        $indexMap = new IndexMap(0);
        $value = $this->createMock(LiteralValueInterface::class);
        $valueList = new LiteralValueList($indexMap, $value);

        $this->expectException(ValueNotFoundException::class);
        $valueList->getValue(1);
    }
}
