<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Exception;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Exception\QueryNotAddressableException;
use Remorhaz\JSON\Path\Query\QueryInterface;

#[CoversClass(QueryNotAddressableException::class)]
class QueryNotAddressableExceptionTest extends TestCase
{
    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new QueryNotAddressableException(
            $this->createMock(QueryInterface::class),
        );
        self::assertSame('Query is not addressable', $exception->getMessage());
    }

    public function testGetQuery_ConstructedWithQuery_ReturnsSameInstance(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $exception = new QueryNotAddressableException($query);
        self::assertSame($query, $exception->getQuery());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new QueryNotAddressableException(
            $this->createMock(QueryInterface::class),
        );
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception();
        $exception = new QueryNotAddressableException(
            $this->createMock(QueryInterface::class),
            $previous,
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
