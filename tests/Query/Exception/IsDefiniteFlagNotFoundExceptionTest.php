<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\IsDefiniteFlagNotFoundException;

/**
 * @covers \Remorhaz\JSON\Path\Query\Exception\IsDefiniteFlagNotFoundException
 */
class IsDefiniteFlagNotFoundExceptionTest extends TestCase
{

    public function testGetMessage_Constructed_ReturnsMatchingValue(): void
    {
        $exception = new IsDefiniteFlagNotFoundException;
        self::assertSame('IsDefinite flag is accessed before being set', $exception->getMessage());
    }

    public function testGetCode_Always_ReturnsZero(): void
    {
        $exception = new IsDefiniteFlagNotFoundException;
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new IsDefiniteFlagNotFoundException;
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new IsDefiniteFlagNotFoundException($previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
