<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\StdDevAggregator;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\StdDevAggregator
 */
class StdDevAggregatorTest extends TestCase
{

    public function testTryAggregate_EmptyArray_ReturnsNull(): void
    {
        $aggregator = new StdDevAggregator;
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([]));
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithSingleElement_ReturnsNull(): void
    {
        $aggregator = new StdDevAggregator;
        $element = $this->createMock(ScalarValueInterface::class);
        $element
            ->method('getData')
            ->willReturn(1);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$element]));
        self::assertNull($aggregator->tryAggregate($value));
    }

    public function testTryAggregate_ArrayWithThreeElements_ReturnsMatchingValue(): void
    {
        $aggregator = new StdDevAggregator;
        $firstElement = $this->createMock(ScalarValueInterface::class);
        $firstElement
            ->method('getData')
            ->willReturn(1);
        $secondElement = $this->createMock(ScalarValueInterface::class);
        $secondElement
            ->method('getData')
            ->willReturn(2);
        $thirdElement = $this->createMock(ScalarValueInterface::class);
        $thirdElement
            ->method('getData')
            ->willReturn(3);
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$firstElement, $secondElement, $thirdElement]));
        self::assertSame(
            1.0,
            $this->exportValueData($aggregator->tryAggregate($value))
        );
    }

    private function exportValueData(?ValueInterface $value)
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
