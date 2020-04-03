<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Mutator\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\Mutator\Exception\ReplaceAtNestedPathsException;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Mutator\Exception\ReplaceAtNestedPathsException
 */
class ReplaceAtNestedPathsExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ReplaceAtNestedPathsException(
            $this->createMock(PathInterface::class),
            $this->createMock(PathInterface::class)
        );
        self::assertSame('Attempt of replacing value at nested paths', $exception->getMessage());
    }

    public function testGetParentPath_ConstructedWithParentPath_ReturnsSameInstance(): void
    {
        $parentPath = $this->createMock(PathInterface::class);
        $exception = new ReplaceAtNestedPathsException(
            $parentPath,
            $this->createMock(PathInterface::class)
        );
        self::assertSame($parentPath, $exception->getParentPath());
    }

    public function testGetNestedPath_ConstructedWithNestedPath_ReturnsSameInstance(): void
    {
        $nestedPath = $this->createMock(PathInterface::class);
        $exception = new ReplaceAtNestedPathsException(
            $this->createMock(PathInterface::class),
            $nestedPath
        );
        self::assertSame($nestedPath, $exception->getNestedPath());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ReplaceAtNestedPathsException(
            $this->createMock(PathInterface::class),
            $this->createMock(PathInterface::class)
        );
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ReplaceAtNestedPathsException(
            $this->createMock(PathInterface::class),
            $this->createMock(PathInterface::class)
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ReplaceAtNestedPathsException(
            $this->createMock(PathInterface::class),
            $this->createMock(PathInterface::class),
            $previous
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
