<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\Aggregator\AvgAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\Exception\AggregateFunctionNotFoundException;
use Remorhaz\JSON\Path\Runtime\Aggregator\LengthAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\MaxAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\MinAggregator;
use Remorhaz\JSON\Path\Runtime\Aggregator\StdDevAggregator;

#[CoversClass(AggregatorCollection::class)]
class AggregatorCollectionTest extends TestCase
{
    /**
     * @param string $name
     * @param class-string $expectedClass
     */
    #[DataProvider('providerByName')]
    public function testByName_KnownName_ReturnsMatchingInstance(string $name, string $expectedClass): void
    {
        $aggregators = new AggregatorCollection();
        self::assertInstanceOf($expectedClass, $aggregators->byName($name));
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function providerByName(): iterable
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
