<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\InvalidScalarDataException;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;

#[CoversClass(LiteralScalarValue::class)]
class LiteralScalarValueTest extends TestCase
{
    public function testConstruct_InvalidValue_ThrowsException(): void
    {
        $this->expectException(InvalidScalarDataException::class);
        new LiteralScalarValue([]);
    }

    #[DataProvider('providerGetData')]
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue(mixed $value, mixed $expectedValue): void
    {
        $value = new LiteralScalarValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function providerGetData(): iterable
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
            'NULL' => [null, null],
            'Integer' => [1, 1],
            'Float' => [1.5, 1.5],
            'String' => ['a', 'a'],
        ];
    }
}
