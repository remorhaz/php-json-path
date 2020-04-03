<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Runtime\Aggregator\Exception\AggregateFunctionNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\Exception\AggregateFunctionNotFoundException
 */
class AggregateFunctionNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new AggregateFunctionNotFoundException('a');
        self::assertSame('Aggregate function not found: a', $exception->getMessage());
    }

    public function testGetName_ConstructedWithName_ReturnsSameValue(): void
    {
        $exception = new AggregateFunctionNotFoundException('a');
        self::assertSame('a', $exception->getName());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new AggregateFunctionNotFoundException('a');
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new AggregateFunctionNotFoundException('a');
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new AggregateFunctionNotFoundException('a', $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
