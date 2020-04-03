<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\EvaluatedValue;

/**
 * @covers \Remorhaz\JSON\Path\Value\EvaluatedValue
 */
class EvaluatedValueTest extends TestCase
{

    /**
     * @param bool $value
     * @param bool $expectedValue
     * @dataProvider providerGetData
     */
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue(bool $value, bool $expectedValue): void
    {
        $value = new EvaluatedValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    public function providerGetData(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
