<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\AvgAggregator;

#[CoversClass(AvgAggregator::class)]
class AvgAggregatorTest extends TestCase
{
    public function testTryAggregate_EmptyArray_ReturnsNull(): void
    {
        $aggregator = new AvgAggregator();
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([]));
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithSingleElement_ReturnsMatchingValue(): void
    {
        $aggregator = new AvgAggregator();
        $element = $this->createMock(ScalarValueInterface::class);
        $element
            ->method('getData')
            ->willReturn(2);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        self::assertSame(2, $this->exportValueData($aggregator->tryAggregate($value)));
    }

    public function testTryAggregate_ArrayWithTwoElements_ReturnsMatchingValue(): void
    {
        $aggregator = new AvgAggregator();
        $firstElement = $this->createMock(ScalarValueInterface::class);
        $firstElement
            ->method('getData')
            ->willReturn(2);
        $secondElement = $this->createMock(ScalarValueInterface::class);
        $secondElement
            ->method('getData')
            ->willReturn(1);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$firstElement, $secondElement]));
        self::assertSame(1.5, $this->exportValueData($aggregator->tryAggregate($value)));
    }

    private function exportValueData(?ValueInterface $value): int|float|string|bool|null
    {
        if (!isset($value)) {
            return null;
        }

        if (!$value instanceof ScalarValueInterface) {
            return null;
        }

        return $value->getData();
    }
}
