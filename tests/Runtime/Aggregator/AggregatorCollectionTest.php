<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\Aggregator\AvgAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\Exception\AggregateFunctionNotFoundException;
use Remorhaz\JSON\Path\Runtime\Aggregator\LengthAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\MaxAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\MinAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\StdDevAggregator;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection
 */
class AggregatorCollectionTest extends TestCase
{
    /**
     * @param string $name
     * @param string $expectedClass
     * @dataProvider providerByName
     */
    public function testByName_KnownName_ReturnsMatchingInstance(string $name, string $expectedClass): void
    {
        $aggregators = new AggregatorCollection();
        self::assertInstanceOf($expectedClass, $aggregators->byName($name));
    }

    public function providerByName(): array
    {
        return [
            'min' => ['min', MinAggregator::class],
            'max' => ['max', MaxAggregator::class],
            'length' => ['length', LengthAggregator::class],
            'avg' => ['avg', AvgAggregator::class],
            'stddev' => ['stddev', StdDevAggregator::class],
        ];
    }

    public function testByName_UnknownName_ThrowsException(): void
    {
        $aggregators = new AggregatorCollection();
        $this->expectException(AggregateFunctionNotFoundException::class);
        $aggregators->byName('a');
    }
}
