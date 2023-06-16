<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\LengthAggregator;

use function array_fill;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\LengthAggregator
 */
class LengthAggregatorTest extends TestCase
{
    public function testTryAggregate_NonArrayValue_ReturnsNull(): void
    {
        $aggregator = new LengthAggregator();
        $value = $this->createMock(ValueInterface::class);
        self::assertNull($aggregator->tryAggregate($value));
    }

    /**
     * @param int   $count
     * @param array $expectedValue
     * @dataProvider providerArrayCount
     */
    public function testTryAggregate_ArrayValue_ReturnsValueWithArrayLength(int $count, array $expectedValue): void
    {
        $aggregator = new LengthAggregator();
        $elements = array_fill(0, $count, $this->createMock(ValueInterface::class));
        $value = $this->createMock(ArrayValueInterface::class);
        $value
            ->method('createChildIterator')
            ->willReturn(new ArrayIterator($elements));
        self::assertSame($expectedValue, $this->exportValueData($aggregator->tryAggregate($value)));
    }

    public function providerArrayCount(): array
    {
        return [
            'Empty array' => [0, ['data' => 0]],
            'Array with single element' => [1, ['data' => 1]],
            'Array with two elements' => [2, ['data' => 2]],
        ];
    }

    private function exportValueData(?ValueInterface $value)
    {
        if (!isset($value)) {
            return null;
        }

        if (!$value instanceof ScalarValueInterface) {
            return null;
        }

        return ['data' => $value->getData()];
    }
}
