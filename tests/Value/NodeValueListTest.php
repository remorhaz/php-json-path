<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMapInterface;
use Remorhaz\JSON\Path\Value\NodeValueList;

/**
 * @covers \Remorhaz\JSON\Path\Value\NodeValueList
 */
class NodeValueListTest extends TestCase
{
    public function testGetIndexMap_ConstructedWithGivenIndexMapInstance_ReturnsSameInstance(): void
    {
        $indexMap = $this->createMock(IndexMapInterface::class);
        $valueList = new NodeValueList($indexMap);
        self::assertSame($indexMap, $valueList->getIndexMap());
    }

    public function testGetValues_ConstructedWithoutValues_ReturnsEmptyArray(): void
    {
        $valueList = new NodeValueList($this->createMock(IndexMapInterface::class));
        self::assertSame([], $valueList->getValues());
    }

    public function testGetValues_ConstructedWithTwoValues_ReturnsSameInstances(): void
    {
        $firstValue = $this->createMock(NodeValueInterface::class);
        $secondValue = $this->createMock(NodeValueInterface::class);
        $valueList = new NodeValueList(
            $this->createMock(IndexMapInterface::class),
            $firstValue,
            $secondValue
        );
        self::assertSame([$firstValue, $secondValue], $valueList->getValues());
    }

    public function testGeValue_ValueExistsAtGivenIndex_ReturnsMatchingInstance(): void
    {
        $value = $this->createMock(NodeValueInterface::class);
        $valueList = new NodeValueList(
            $this->createMock(IndexMapInterface::class),
            $value
        );
        self::assertSame($value, $valueList->getValue(0));
    }

    public function testGeValue_ValueNotExistsAtGivenIndex_ThrowsException(): void
    {
        $valueList = new NodeValueList($this->createMock(IndexMapInterface::class));

        $this->expectException(ValueNotFoundException::class);
        $valueList->getValue(0);
    }
}
