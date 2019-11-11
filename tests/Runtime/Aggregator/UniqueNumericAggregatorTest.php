<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\UniqueNumericAggregator;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\UniqueNumericAggregator
 */
class UniqueNumericAggregatorTest extends TestCase
{

    public function testTryAggregate_ArrayWithDifferentValues_AggregatesBothValues(): void
    {
        $aggregator = $this->getMockForAbstractClass(UniqueNumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $firstElement = $this->createMock(ScalarValueInterface::class);
        $firstElement
            ->method('getData')
            ->willReturn(1);
        $secondElement = $this->createMock(ScalarValueInterface::class);
        $secondElement
            ->method('getData')
            ->willReturn(1.2);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$firstElement, $secondElement]));
        $aggregator
            ->expects(self::once())
            ->method('aggregateNumericData')
            ->with(
                self::identicalTo([1, 1.2]),
                self::identicalTo($firstElement),
                self::identicalTo($secondElement)
            );
        $aggregator->tryAggregate($value);
    }

    public function testTryAggregate_ArrayWithEqualValues_AggregatesOnlyFirstValue(): void
    {
        $aggregator = $this->getMockForAbstractClass(UniqueNumericAggregator::class);
        $value = $this->createMock(ArrayValueInterface::class);
        $firstElement = $this->createMock(ScalarValueInterface::class);
        $firstElement
            ->method('getData')
            ->willReturn(1);
        $secondElement = $this->createMock(ScalarValueInterface::class);
        $secondElement
            ->method('getData')
            ->willReturn(1);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator([$firstElement, $secondElement]));
        $aggregator
            ->expects(self::once())
            ->method('aggregateNumericData')
            ->with(
                self::identicalTo([1]),
                self::identicalTo($firstElement)
            );
        $aggregator->tryAggregate($value);
    }
}
