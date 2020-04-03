<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Aggregator\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\Exception\MinElementNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Aggregator\Exception\MinElementNotFoundException
 */
class MinElementNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new MinElementNotFoundException([], []);
        self::assertSame('Min element not found', $exception->getMessage());
    }

    public function testGetDataLiist_ConstructedWithDataList_ReturnsSameValue(): void
    {
        $exception = new MinElementNotFoundException(['a'], []);
        self::assertSame(['a'], $exception->getDataList());
    }

    public function testGetElements_ConstructedWithElements_ReturnsSameInstances(): void
    {
        $element = $this->createMock(ScalarValueInterface::class);
        $exception = new MinElementNotFoundException([], [$element]);
        self::assertSame([$element], $exception->getElements());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new MinElementNotFoundException([], []);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new MinElementNotFoundException([], []);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new MinElementNotFoundException([], [], $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
