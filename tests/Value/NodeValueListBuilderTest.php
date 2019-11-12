<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;

/**
 * @covers \Remorhaz\JSON\Path\Value\NodeValueListBuilder
 */
class NodeValueListBuilderTest extends TestCase
{

    public function testBuild_NoValuesAdded_ReturnsListWithEmptyIndexMap(): void
    {
        $builder = new NodeValueListBuilder;
        $values = $builder->build();
        self::assertSame([], $values->getIndexMap()->getOuterIndexes());
    }

    public function testBuild_NoValuesAdded_ReturnsEmptyList(): void
    {
        $builder = new NodeValueListBuilder;
        $values = $builder->build();
        self::assertSame([], $values->getValues());
    }

    public function testBuild_ValueAdded_ReturnsListWithSameValueInstance(): void
    {
        $builder = new NodeValueListBuilder;
        $value = $this->createMock(NodeValueInterface::class);
        $builder->addValue($value, 1);
        $values = $builder->build();
        self::assertSame([$value], $values->getValues());
    }

    public function testBuild_ValuesAdded_ReturnsListWithSameValueInstances(): void
    {
        $builder = new NodeValueListBuilder;
        $firstValue = $this->createMock(NodeValueInterface::class);
        $secondValue = $this->createMock(NodeValueInterface::class);
        $builder->addValue($firstValue, 1);
        $builder->addValue($secondValue, 1);
        $values = $builder->build();
        self::assertSame([$firstValue, $secondValue], $values->getValues());
    }

    public function testBuild_ValueAddedWithGivenOuterIndex_ReturnsListWithSameOuterIndexInMap(): void
    {
        $builder = new NodeValueListBuilder;
        $value = $this->createMock(NodeValueInterface::class);
        $builder->addValue($value, 1);
        $values = $builder->build();
        self::assertSame([1], $values->getIndexMap()->getOuterIndexes());
    }

    public function testBuild_ValuesAddedWithGivenOuterIndex_ReturnsListWithSameOuterIndexesInMap(): void
    {
        $builder = new NodeValueListBuilder;
        $value = $this->createMock(NodeValueInterface::class);
        $builder->addValue($value, 1);
        $builder->addValue($value, 2);
        $values = $builder->build();
        self::assertSame([1, 2], $values->getIndexMap()->getOuterIndexes());
    }

    public function testAddValue_Constructed_ReturnsSelf(): void
    {
        $builder = new NodeValueListBuilder;
        $value = $this->createMock(NodeValueInterface::class);
        self::assertSame($builder, $builder->addValue($value, 1));
    }
}