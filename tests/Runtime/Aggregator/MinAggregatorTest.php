<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\MinAggregator;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\MinAggregator
 */
class MinAggregatorTest extends TestCase
{

    public function testTryAggregate_ArrayWithZeroElement_ReturnsNull(): void
    {
        $aggregator = new MinAggregator();
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([]));
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithSingleElement_ReturnsSameElement(): void
    {
        $aggregator = new MinAggregator();
        $element = $this->createMock(ScalarValueInterface::class);
        $element
            ->method('getData')
            ->willReturn(1);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        self::assertSame($element, $aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithTwoElements_ReturnsGreaterElement(): void
    {
        $aggregator = new MinAggregator();
        $lesserElement = $this->createMock(ScalarValueInterface::class);
        $lesserElement
            ->method('getData')
            ->willReturn(1);
        $greaterElement = $this->createMock(ScalarValueInterface::class);
        $greaterElement
            ->method('getData')
            ->willReturn(2);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$lesserElement, $greaterElement]));
        self::assertSame($lesserElement, $aggregator->tryAggregate($value));
    }
}
