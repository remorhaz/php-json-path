<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Exception\IndefiniteQueryException;
use Remorhaz\JSON\Path\Processor\Exception\QueryNotAddressableException;
use Remorhaz\JSON\Path\Query\Capabilities;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Query\QueryValidator;

/**
 * @covers \Remorhaz\JSON\Path\Query\QueryValidator
 */
class QueryValidatorTest extends TestCase
{

    public function testGetDefiniteQuery_GivenDefiniteQuery_ReturnsSameInstance(): void
    {
        $capabilities = new Capabilities(true, false);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($capabilities);

        $actualValue = (new QueryValidator())->getDefiniteQuery($query);
        self::assertSame($query, $actualValue);
    }

    public function testGetDefiniteQuery_GivenIndefiniteQuery_ThrowsException(): void
    {
        $capabilities = new Capabilities(false, false);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $validator = new QueryValidator();

        $this->expectException(IndefiniteQueryException::class);
        $validator->getDefiniteQuery($query);
    }

    public function testGetAddressableQuery_GivenAddressableQuery_ReturnsSameInstance(): void
    {
        $capabilities = new Capabilities(false, true);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($capabilities);

        $actualValue = (new QueryValidator())->getAddressableQuery($query);
        self::assertSame($query, $actualValue);
    }

    public function testGetAddressableQuery_GivenNotAddressableQuery_ThrowsException(): void
    {
        $capabilities = new Capabilities(false, false);
        $query = $this->createMock(QueryInterface::class);
        $query
            ->method('getCapabilities')
            ->willReturn($capabilities);
        $validator = new QueryValidator();

        $this->expectException(QueryNotAddressableException::class);
        $validator->getAddressableQuery($query);
    }
}
