<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\OuterIndexNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMapInterface;

/**
 * @covers \Remorhaz\JSON\Path\Value\Exception\OuterIndexNotFoundException
 */
class OuterIndexNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new OuterIndexNotFoundException(
            1,
            $this->createMock(IndexMapInterface::class)
        );
        self::assertSame('Outer index not found in index map for inner index 1', $exception->getMessage());
    }

    public function testGetInnerIndex_ConstructedWithGivenInnerIndex_ReturnsSameValue(): void
    {
        $exception = new OuterIndexNotFoundException(
            1,
            $this->createMock(IndexMapInterface::class)
        );
        self::assertSame(1, $exception->getInnerIndex());
    }

    public function testGetIndexMap_ConstructedWithGivenIndexMap_ReturnsSameInstance(): void
    {
        $indexMap = $this->createMock(IndexMapInterface::class);
        $exception = new OuterIndexNotFoundException(1, $indexMap);
        self::assertSame($indexMap, $exception->getIndexMap());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new OuterIndexNotFoundException(
            1,
            $this->createMock(IndexMapInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new OuterIndexNotFoundException(
            1,
            $this->createMock(IndexMapInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new OuterIndexNotFoundException(
            1,
            $this->createMock(IndexMapInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
