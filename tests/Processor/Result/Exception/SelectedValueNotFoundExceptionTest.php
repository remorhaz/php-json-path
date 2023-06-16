<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Result\Exception\SelectedValueNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\Exception\SelectedValueNotFoundException
 */
class SelectedValueNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new SelectedValueNotFoundException();
        self::assertSame('Selected value not found', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new SelectedValueNotFoundException();
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new SelectedValueNotFoundException();
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new SelectedValueNotFoundException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
