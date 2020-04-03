<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\NumericAggregator;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\NumericAggregator
 */
class NumericAggregatorTest extends TestCase
{

    public function testTryAggregate_NonArrayValue_ReturnsNull(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ValueInterface::class);
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithNonScalarElement_ReturnsNull(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $element = $this->createMock(ValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithNonNumericScalarElement_ReturnsNull(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $element = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        $element
            ->method('getData')
            ->willReturn('a');
        self::assertNull($aggregator->tryAggregate($value));
    }

    /**
     * @param int|float $data
     * @dataProvider providerNumericData
     */
    public function testTryAggregate_ArrayWithNumericScalarElement_AggregatesElement($data): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $element = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        $element
            ->method('getData')
            ->willReturn($data);
        $aggregator
            ->expects(self::once())
            ->method('aggregateNumericData')
            ->with(
                self::identicalTo([$data]),
                self::identicalTo($element)
            );
        $aggregator->tryAggregate($value);
    }

    public function providerNumericData(): array
    {
        return [
            'Integer data' => [1],
            'Float data' => [1.2],
        ];
    }

    public function testTryAggregate_ArrayWithNumericAndNonNumericElements_AggregatesOnlyNumericElements(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $numericElement = $this->createMock(ScalarValueInterface::class);
        $numericElement
            ->method('getData')
            ->willReturn(1);
        $nonNumericElement = $this->createMock(ScalarValueInterface::class);
        $nonNumericElement
            ->method('getData')
            ->willReturn('a');
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$nonNumericElement, $numericElement]));
        $aggregator
            ->expects(self::once())
            ->method('aggregateNumericData')
            ->with(
                self::identicalTo([1]),
                self::identicalTo($numericElement)
            );
        $aggregator->tryAggregate($value);
    }

    public function testTryAggregate_AggregationReturnsValue_ReturnsSameInstance(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $element = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        $element
            ->method('getData')
            ->willReturn(1);
        $result = $this->createMock(ValueInterface::class);
        $aggregator
            ->method('aggregateNumericData')
            ->willReturn($result);
        self::assertSame($result, $aggregator->tryAggregate($value));
    }

    public function testTryAggregate_AggregationReturnsNull_ReturnsNull(): void
    {
        $aggregator = $this->getMockForAbstractClass(NumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $element = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        $element
            ->method('getData')
            ->willReturn(1);
        $aggregator
            ->method('aggregateNumericData')
            ->willReturn(null);
        self::assertNull($aggregator->tryAggregate($value));
    }
}
