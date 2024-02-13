<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\ReferenceNotFoundException;

#[CoversClass(ReferenceNotFoundException::class)]
class ReferenceNotFoundExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new ReferenceNotFoundException(1);
        self::assertSame('Reference #1 not found', $exception->getMessage());
    }

    public function testGetReferenceId_ConstructedWithReferenceId_ReturnsSameValue(): void
    {
        $exception = new ReferenceNotFoundException(1);
        self::assertSame(1, $exception->getReferenceId());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new ReferenceNotFoundException(1);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new ReferenceNotFoundException(0);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new ReferenceNotFoundException(0, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
