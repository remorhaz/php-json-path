<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollectionInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\ValueAggregatorInterface;
use Remorhaz\JSON\Path\Runtime\Comparator\ComparatorCollectionInterface;
use Remorhaz\JSON\Path\Runtime\Comparator\ComparatorInterface;
use Remorhaz\JSON\Path\Runtime\Evaluator;
use Remorhaz\JSON\Path\Runtime\Exception\IndexMapMatchFailedException;
use Remorhaz\JSON\Path\Runtime\Exception\InvalidRegExpException;
use Remorhaz\JSON\Path\Runtime\Exception\LiteralEvaluationFailedException;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\IndexMap;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;
use Remorhaz\JSON\Path\Value\LiteralValueListInterface;
use Remorhaz\JSON\Path\Value\ValueList;
use Remorhaz\JSON\Path\Value\ValueListInterface;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Evaluator
 */
class EvaluatorTest extends TestCase
{

    public function testLogicalOr_EmptyLists_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->logicalOr(
            new EvaluatedValueList(new IndexMap),
            new EvaluatedValueList(new IndexMap)
        );
        self::assertSame([], $result->getResults());
    }

    /**
     * @param bool  $leftResult
     * @param bool  $rightResult
     * @param array $expectedValues
     * @dataProvider providerLogicalOr
     */
    public function testLogicalOr_CompatibleLists_ReturnsMatching(
        bool $leftResult,
        bool $rightResult,
        array $expectedValues
    ): void {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->logicalOr(
            new EvaluatedValueList(new IndexMap, $leftResult),
            new EvaluatedValueList(new IndexMap, $rightResult)
        );
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerLogicalOr(): array
    {
        return [
            'Both false' => [false, false, [false]],
            'Left false' => [false, true, [true]],
            'Right false' => [true, false, [true]],
            'Both true' => [true, true, [true]],
        ];
    }

    public function testLogicalOr_IncompatibleIndexMaps_ThrowsException(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValues = new EvaluatedValueList(new IndexMap(1));
        $rightValues = new EvaluatedValueList(new IndexMap(2, 3));
        $this->expectException(IndexMapMatchFailedException::class);
        $evaluator->logicalOr($leftValues, $rightValues);
    }

    public function testLogicalAnd_EmptyLists_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->logicalAnd(
            new EvaluatedValueList(new IndexMap),
            new EvaluatedValueList(new IndexMap)
        );
        self::assertSame([], $result->getResults());
    }

    /**
     * @param bool  $leftResult
     * @param bool  $rightResult
     * @param array $expectedValues
     * @dataProvider providerLogicalAnd
     */
    public function testLogicalAnd_CompatibleLists_ReturnsMatching(
        bool $leftResult,
        bool $rightResult,
        array $expectedValues
    ): void {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->logicalAnd(
            new EvaluatedValueList(new IndexMap, $leftResult),
            new EvaluatedValueList(new IndexMap, $rightResult)
        );
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerLogicalAnd(): array
    {
        return [
            'Both false' => [false, false, [false]],
            'Left false' => [false, true, [false]],
            'Right false' => [true, false, [false]],
            'Both true' => [true, true, [true]],
        ];
    }

    public function testLogicalAnd_IncompatibleIndexMaps_ThrowsException(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValues = new EvaluatedValueList(new IndexMap(1));
        $rightValues = new EvaluatedValueList(new IndexMap(2, 3));
        $this->expectException(IndexMapMatchFailedException::class);
        $evaluator->logicalAnd($leftValues, $rightValues);
    }

    public function testLogicalNot_NoValueInList_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = new EvaluatedValueList(new IndexMap);
        $result = $evaluator->logicalNot($values);
        self::assertSame([], $result->getResults());
    }

    public function testLogicalNot_ListWithIndexMap_ReturnsListWithSameMapInstance(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $indexMap = new IndexMap;
        $values = new EvaluatedValueList($indexMap);
        $result = $evaluator->logicalNot($values);
        self::assertSame($indexMap, $result->getIndexMap());
    }

    /**
     * @param bool  $value
     * @param array $expectedValues
     * @dataProvider providerLogicalNot
     */
    public function testLogicalNot_ValueInList_ReturnsMatchingList(bool $value, array $expectedValues): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = new EvaluatedValueList(new IndexMap, $value);
        $result = $evaluator->logicalNot($values);
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerLogicalNot(): array
    {
        return [
            'True becomes false' => [true, [false]],
            'False becomes true' => [false, [true]],
        ];
    }

    public function testIsEqual_EmptyIndexMapsInLists_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isEqual(
            new ValueList(new IndexMap),
            new ValueList(new IndexMap)
        );
        self::assertSame([], $result->getResults());
    }

    /**
     * @param int|null $leftOuterIndex
     * @param int|null $rightOuterIndex
     * @dataProvider providerNullOuterIndex
     */
    public function testIsEqual_NullIndexMapsInLists_ReturnsEmptyList(
        ?int $leftOuterIndex,
        ?int $rightOuterIndex
    ): void {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isEqual(
            new ValueList(new IndexMap($leftOuterIndex)),
            new ValueList(new IndexMap($rightOuterIndex))
        );
        self::assertSame([], $result->getResults());
    }

    public function providerNullOuterIndex(): array
    {
        return [
            'Both indexes are null' => [null, null],
            'Left index is null' => [null, 1],
            'Right index is null' => [1, null],
        ];
    }

    public function testIsEqual_IndexMapsWithDifferentOuterIndexesInLists_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isEqual(
            new ValueList(new IndexMap(1)),
            new ValueList(new IndexMap(2))
        );
        self::assertSame([], $result->getResults());
    }

    public function testIsEqual_IndexMapsWithSameOuterIndexesInLists_PassesValuesToComparator(): void
    {
        $comparators = $this->createMock(ComparatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $comparators,
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValue = $this->createMock(ValueInterface::class);
        $rightValue = $this->createMock(ValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $comparators
            ->method('equal')
            ->willReturn($comparator);

        $comparator
            ->expects(self::once())
            ->method('compare')
            ->with(self::identicalTo($leftValue), self::identicalTo($rightValue));
        $evaluator->isEqual(
            new ValueList(new IndexMap(1), $leftValue),
            new ValueList(new IndexMap(1), $rightValue)
        );
    }

    /**
     * @param bool  $comparison
     * @param array $expectedValues
     * @dataProvider providerIsEqual
     */
    public function testIsEqual_ComparatorReturnsResult_ReturnsListWithSameResult(
        bool $comparison,
        array $expectedValues
    ): void {
        $comparators = $this->createMock(ComparatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $comparators,
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValue = $this->createMock(ValueInterface::class);
        $rightValue = $this->createMock(ValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $comparators
            ->method('equal')
            ->willReturn($comparator);

        $comparator
            ->method('compare')
            ->willReturn($comparison);
        $result = $evaluator->isEqual(
            new ValueList(new IndexMap(1), $leftValue),
            new ValueList(new IndexMap(1), $rightValue)
        );
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerIsEqual(): array
    {
        return [
            'True' => [true, [true]],
            'False' => [false, [false]],
        ];
    }

    public function testIsEqual_IndexMapsWithSameOuterIndexesInLists_ReturnsListWithSameOuterIndexInMap(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isEqual(
            new ValueList(new IndexMap(1), $this->createMock(ValueInterface::class)),
            new ValueList(new IndexMap(1), $this->createMock(ValueInterface::class))
        );
        self::assertSame([1], $result->getIndexMap()->getOuterIndexes());
    }

    public function testIsGreater_IndexMapsWithDifferentOuterIndexesInLists_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isGreater(
            new ValueList(new IndexMap(1)),
            new ValueList(new IndexMap(2))
        );
        self::assertSame([], $result->getResults());
    }

    public function testIsGreater_IndexMapsWithSameOuterIndexesInLists_PassesValuesToComparator(): void
    {
        $comparators = $this->createMock(ComparatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $comparators,
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValue = $this->createMock(ValueInterface::class);
        $rightValue = $this->createMock(ValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $comparators
            ->method('greater')
            ->willReturn($comparator);

        $comparator
            ->expects(self::once())
            ->method('compare')
            ->with(self::identicalTo($leftValue), self::identicalTo($rightValue));
        $evaluator->isGreater(
            new ValueList(new IndexMap(1), $leftValue),
            new ValueList(new IndexMap(1), $rightValue)
        );
    }

    /**
     * @param bool  $comparison
     * @param array $expectedValues
     * @dataProvider providerIsGreater
     */
    public function testIsGreater_ComparatorReturnsResult_ReturnsListWithSameResult(
        bool $comparison,
        array $expectedValues
    ): void {
        $comparators = $this->createMock(ComparatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $comparators,
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $leftValue = $this->createMock(ValueInterface::class);
        $rightValue = $this->createMock(ValueInterface::class);
        $comparator = $this->createMock(ComparatorInterface::class);
        $comparators
            ->method('greater')
            ->willReturn($comparator);

        $comparator
            ->method('compare')
            ->willReturn($comparison);
        $result = $evaluator->isGreater(
            new ValueList(new IndexMap(1), $leftValue),
            new ValueList(new IndexMap(1), $rightValue)
        );
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerIsGreater(): array
    {
        return [
            'True' => [true, [true]],
            'False' => [false, [false]],
        ];
    }

    public function testIsGreater_IndexMapsWithSameOuterIndexesInLists_ReturnsListWithSameOuterIndexInMap(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $result = $evaluator->isGreater(
            new ValueList(new IndexMap(1), $this->createMock(ValueInterface::class)),
            new ValueList(new IndexMap(1), $this->createMock(ValueInterface::class))
        );
        self::assertSame([1], $result->getIndexMap()->getOuterIndexes());
    }

    public function testIsRegExp_EmptyList_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = new ValueList(new IndexMap);
        $result = $evaluator->isRegExp('//', $values);
        self::assertSame([], $result->getResults());
    }

    public function testIsRegExp_NonScalarValueInList_ReturnsFalseResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = new ValueList(new IndexMap(1), $this->createMock(ValueInterface::class));
        $result = $evaluator->isRegExp('//', $values);
        self::assertSame([false], $result->getResults());
    }

    public function testIsRegExp_NonStringScalarValueInList_ReturnsFalseResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn(1);
        $values = new ValueList(new IndexMap(1), $value);
        $result = $evaluator->isRegExp('//', $values);
        self::assertSame([false], $result->getResults());
    }

    public function testIsRegExp_InvalidRegExp_ThrowsException(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn('a');
        $values = new ValueList(new IndexMap(1), $value);
        $this->expectException(InvalidRegExpException::class);
        $evaluator->isRegExp('/', $values);
    }

    public function testIsRegExp_NonMatchingStringScalarValueInList_ReturnsFalseResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn('a');
        $values = new ValueList(new IndexMap(1), $value);
        $result = $evaluator->isRegExp('/b/', $values);
        self::assertSame([false], $result->getResults());
    }

    public function testIsRegExp_MatchingStringScalarValueInList_ReturnsTrueResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn('a');
        $values = new ValueList(new IndexMap(1), $value);
        $result = $evaluator->isRegExp('/a/', $values);
        self::assertSame([true], $result->getResults());
    }

    public function testIsRegExp_IndexMapInList_ReturnsListWithSameIndexMapInstance(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $indexMap = new IndexMap;
        $values = new ValueList($indexMap);
        $result = $evaluator->isRegExp('//', $values);
        self::assertSame($indexMap, $result->getIndexMap());
    }

    public function testEvaluate_EvaluatedResults_ReturnsSameInstance(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = $this->createMock(EvaluatedValueListInterface::class);
        $result = $evaluator->evaluate(
            $this->createMock(ValueListInterface::class),
            $values
        );
        self::assertSame($values, $result);
    }

    public function testEvaluate_EmptyIndexMapInSource_ReturnsEmptyResult(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $source = new ValueList(new IndexMap);
        $result = $evaluator->evaluate(
            $source,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame([], $result->getResults());
    }

    public function testEvaluate_NullOuterIndexInSource_ReturnsFalseResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $source = new ValueList(new IndexMap(null));
        $result = $evaluator->evaluate(
            $source,
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame([false], $result->getResults());
    }

    public function testEvaluate_OuterIndexInSourceNotExistsInValues_ReturnsFalseResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $source = new ValueList(new IndexMap(1));
        $result = $evaluator->evaluate(
            $source,
            new ValueList(new IndexMap(2))
        );
        self::assertSame([false], $result->getResults());
    }

    public function testEvaluate_OuterIndexInSourceExistsInValues_ReturnsTrueResultInList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $source = new ValueList(new IndexMap(1));
        $result = $evaluator->evaluate(
            $source,
            new ValueList(new IndexMap(1))
        );
        self::assertSame([true], $result->getResults());
    }

    public function testEvaluate_NonScalarLiteralInList_ThrowsException(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = $this->createMock(LiteralValueListInterface::class);
        $values
            ->method('getIndexMap')
            ->willReturn(new IndexMap);
        $values
            ->method('getLiteral')
            ->willReturn($this->createMock(LiteralValueInterface::class));
        $source = new ValueList(new IndexMap);
        $this->expectException(LiteralEvaluationFailedException::class);
        $evaluator->evaluate($source, $values);
    }

    public function testEvaluate_NonBooleanScalarLiteralInList_ThrowsException(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = $this->createMock(LiteralValueListInterface::class);
        $values
            ->method('getIndexMap')
            ->willReturn(new IndexMap);
        $values
            ->method('getLiteral')
            ->willReturn(new LiteralScalarValue(1));
        $source = new ValueList(new IndexMap);
        $this->expectException(LiteralEvaluationFailedException::class);
        $evaluator->evaluate($source, $values);
    }

    /**
     * @param bool  $data
     * @param array $expectedValues
     * @dataProvider providerEvaluateLiteral
     */
    public function testEvaluate_BooleanScalarLiteralInList_ReturnsMatchingResultInList(
        bool $data,
        array $expectedValues
    ): void {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = $this->createMock(LiteralValueListInterface::class);
        $values
            ->method('getIndexMap')
            ->willReturn(new IndexMap(1));
        $values
            ->method('getLiteral')
            ->willReturn(new LiteralScalarValue($data));
        $source = new ValueList(new IndexMap(1));
        $result = $evaluator->evaluate($source, $values);
        self::assertSame($expectedValues, $result->getResults());
    }

    public function providerEvaluateLiteral(): array
    {
        return [
            'True' => [true, [true]],
            'False' => [false, [false]],
        ];
    }

    public function testAggregate_NoValueInList_NeverCallsAggregator(): void
    {
        $aggregators = $this->createMock(AggregatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $aggregators
        );
        $aggregator = $this->createMock(ValueAggregatorInterface::class);
        $aggregators
            ->method('byName')
            ->with(self::identicalTo('a'))
            ->willReturn($aggregator);
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([]);

        $aggregator
            ->expects(self::never())
            ->method('tryAggregate');
        $evaluator->aggregate('a', $values);
    }

    public function testAggregate_NoValueInList_ReturnsEmptyList(): void
    {
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $this->createMock(AggregatorCollectionInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([]);

        $result = $evaluator->aggregate('a', $values);
        self::assertSame([], $result->getValues());
    }

    public function testAggregate_ValueInList_PassesSameInstanceToAggregator(): void
    {
        $aggregators = $this->createMock(AggregatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $aggregators
        );
        $aggregator = $this->createMock(ValueAggregatorInterface::class);
        $aggregators
            ->method('byName')
            ->with(self::identicalTo('a'))
            ->willReturn($aggregator);
        $value = $this->createMock(ValueInterface::class);
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);

        $aggregator
            ->expects(self::once())
            ->method('tryAggregate')
            ->with($value);
        $evaluator->aggregate('a', $values);
    }

    public function testAggregate_AggregatorReturnsNull_ReturnsEmptyList(): void
    {
        $aggregators = $this->createMock(AggregatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $aggregators
        );
        $aggregator = $this->createMock(ValueAggregatorInterface::class);
        $aggregators
            ->method('byName')
            ->with(self::identicalTo('a'))
            ->willReturn($aggregator);

        $value = $this->createMock(ValueInterface::class);
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);

        $aggregator
            ->method('tryAggregate')
            ->willReturn(null);
        $result = $evaluator->aggregate('a', $values);
        self::assertSame([], $result->getValues());
    }

    public function testAggregate_AggregatorReturnsValue_ReturnsListWithSameInstance(): void
    {
        $aggregators = $this->createMock(AggregatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $aggregators
        );
        $aggregator = $this->createMock(ValueAggregatorInterface::class);
        $aggregators
            ->method('byName')
            ->with(self::identicalTo('a'))
            ->willReturn($aggregator);

        $value = $this->createMock(ValueInterface::class);
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);

        $aggregatedValue = $this->createMock(ValueInterface::class);
        $aggregator
            ->method('tryAggregate')
            ->willReturn($aggregatedValue);
        $result = $evaluator->aggregate('a', $values);
        self::assertSame([$aggregatedValue], $result->getValues());
    }

    public function testAggregate_AggregatorReturnsValue_ReturnsListWithMatchingIndexMap(): void
    {
        $aggregators = $this->createMock(AggregatorCollectionInterface::class);
        $evaluator = new Evaluator(
            $this->createMock(ComparatorCollectionInterface::class),
            $aggregators
        );
        $aggregator = $this->createMock(ValueAggregatorInterface::class);
        $aggregators
            ->method('byName')
            ->with(self::identicalTo('a'))
            ->willReturn($aggregator);

        $value = $this->createMock(ValueInterface::class);
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getIndexMap')
            ->willReturn(new IndexMap(1));

        $aggregatedValue = $this->createMock(ValueInterface::class);
        $aggregator
            ->method('tryAggregate')
            ->willReturn($aggregatedValue);
        $result = $evaluator->aggregate('a', $values);
        self::assertSame([1], $result->getIndexMap()->getOuterIndexes());
    }
}
