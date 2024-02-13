<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\EvaluatedValue;

#[CoversClass(EvaluatedValue::class)]
class EvaluatedValueTest extends TestCase
{
    #[DataProvider('providerGetData')]
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue(bool $value, bool $expectedValue): void
    {
        $value = new EvaluatedValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    /**
     * @return iterable<string, array{bool, bool}>
     */
    public static function providerGetData(): iterable
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }
}
