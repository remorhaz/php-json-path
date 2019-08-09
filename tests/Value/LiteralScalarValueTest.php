<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\Exception\InvalidScalarDataException;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;

/**
 * @covers \Remorhaz\JSON\Path\Value\LiteralScalarValue
 */
class LiteralScalarValueTest extends TestCase
{

    public function testConstruct_InvalidValue_ThrowsException(): void
    {
        $this->expectException(InvalidScalarDataException::class);
        new LiteralScalarValue([]);
    }

    /**
     * @param mixed $value
     * @param mixed $expectedValue
     * @dataProvider providerGetData
     */
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue($value, $expectedValue): void
    {
        $value = new LiteralScalarValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    public function providerGetData(): array
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
