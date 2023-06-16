<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValue;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\Exception\ResultNotFoundException;
use Remorhaz\JSON\Path\Value\Exception\ValueNotFoundException;
use Remorhaz\JSON\Path\Value\IndexMap;

use function array_map;
use function get_class;

/**
 * @covers \Remorhaz\JSON\Path\Value\EvaluatedValueList
 */
class EvaluatedValueListTest extends TestCase
{
    public function testGetIndexMap_ConstructedWithIndexMap_ReturnsSameInstance(): void
    {
        $indexMap = new IndexMap();
        $values = new EvaluatedValueList($indexMap);
        self::assertSame($indexMap, $values->getIndexMap());
    }

    public function testGetResults_ConstructedWithoutResults_ReturnsEmptyArray(): void
    {
        $values = new EvaluatedValueList(new IndexMap());
        self::assertSame([], $values->getResults());
    }

    /**
     * @param bool[] $results
     * @param bool[] $expectedValues
     * @dataProvider providerResults
     */
    public function testGetResults_ConstructedWithResults_ReturnsSameResults(
        array $results,
        array $expectedValues
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValues, $values->getResults());
    }

    public function providerResults(): array
    {
        return [
            'Single TRUE' => [[true], [true]],
            'Single FALSE' => [[false], [false]],
            'Sequence' => [[true, false], [true, false]],
        ];
    }

    public function testGetResult_NonExistingIndex_ThrowsException(): void
    {
        $values = new EvaluatedValueList(new IndexMap());
        $this->expectException(ResultNotFoundException::class);
        $values->getResult(0);
    }

    /**
     * @param bool[] $results
     * @param int    $index
     * @param bool   $expectedValue
     * @dataProvider providerResult
     */
    public function testGetResult_ExistingIndex_ReturnsMatchingResult(
        array $results,
        int $index,
        bool $expectedValue
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValue, $values->getResult($index));
    }

    public function providerResult(): array
    {
        return [
            'First FALSE' => [[false, true], 0, false],
            'Second TRUE' => [[false, true], 1, true],
        ];
    }

    public function testGetValue_NonExistingIndex_ThrowsException(): void
    {
        $values = new EvaluatedValueList(new IndexMap());
        $this->expectException(ValueNotFoundException::class);
        $values->getValue(0);
    }

    /**
     * @param bool[] $results
     * @param int    $index
     * @param array  $expectedValue
     * @dataProvider providerValue
     */
    public function testGetValue_ExistingIndex_ReturnsMatchingValue(
        array $results,
        int $index,
        array $expectedValue
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValue, $this->exportValue($values->getValue($index)));
    }

    public function providerValue(): array
    {
        return [
            'First FALSE' => [
                [false, true],
                0,
                [
                    'class' => EvaluatedValue::class,
                    'data' => false,
                ],
            ],
            'Second TRUE' => [
                [false, true],
                1,
                [
                    'class' => EvaluatedValue::class,
                    'data' => true,
                ],
            ],
        ];
    }

    public function testGetValue_CalledTwice_ReturnsSameInstance(): void
    {
        $values = new EvaluatedValueList(new IndexMap(), true);
        $value = $values->getValue(0);
        self::assertSame($value, $values->getValue(0));
    }

    public function testGetValues_ConstructedWithoutResults_ReturnsEmptyArray(): void
    {
        $values = new EvaluatedValueList(new IndexMap());
        self::assertSame([], $values->getValues());
    }

    /**
     * @param array $results
     * @param array $expectedValues
     * @dataProvider providerGetValues
     */
    public function testGetValues_ConstructedWithResults_ReturnsMatchingValues(
        array $results,
        array $expectedValues
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValues, $this->exportValues(...$values->getValues()));
    }

    public function providerGetValues(): array
    {
        return [
            'Two different results' => [
                [true, false],
                [
                    [
                        'class' => EvaluatedValue::class,
                        'data' => true,
                    ],
                    [
                        'class' => EvaluatedValue::class,
                        'data' => false,
                    ],
                ],
            ],
        ];
    }

    private function exportValue(ValueInterface $value): array
    {
        $data = [
            'class' => get_class($value),
        ];

        if ($value instanceof ScalarValueInterface) {
            $data += [
                'data' => $value->getData(),
            ];
        }

        return $data;
    }

    private function exportValues(ValueInterface ...$values): array
    {
        return array_map([$this, 'exportValue'], $values);
    }
}
