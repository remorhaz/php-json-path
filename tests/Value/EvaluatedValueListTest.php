<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

#[CoversClass(EvaluatedValueList::class)]
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
     * @param list<bool> $results
     * @param list<bool> $expectedValues
     */
    #[DataProvider('providerResults')]
    public function testGetResults_ConstructedWithResults_ReturnsSameResults(
        array $results,
        array $expectedValues,
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValues, $values->getResults());
    }

    /**
     * @return iterable<string, array{bool, list<bool>}>
     */
    public static function providerResults(): iterable
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
     * @param list<bool> $results
     * @param int        $index
     * @param bool       $expectedValue
     */
    #[DataProvider('providerResult')]
    public function testGetResult_ExistingIndex_ReturnsMatchingResult(
        array $results,
        int $index,
        bool $expectedValue,
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValue, $values->getResult($index));
    }

    /**
     * @return iterable<string, array{list<bool>, int, bool}>
     */
    public static function providerResult(): iterable
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
     * @param list<bool> $results
     * @param int        $index
     * @param array      $expectedValue
     */
    #[DataProvider('providerValue')]
    public function testGetValue_ExistingIndex_ReturnsMatchingValue(
        array $results,
        int $index,
        array $expectedValue,
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValue, $this->exportValue($values->getValue($index)));
    }

    /**
     * @return iterable<string, array{list<bool>, int, array}>
     */
    public static function providerValue(): iterable
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
     * @param list<bool> $results
     * @param array      $expectedValues
     */
    #[DataProvider('providerGetValues')]
    public function testGetValues_ConstructedWithResults_ReturnsMatchingValues(
        array $results,
        array $expectedValues,
    ): void {
        $values = new EvaluatedValueList(new IndexMap(), ...$results);
        self::assertSame($expectedValues, $this->exportValues(...$values->getValues()));
    }

    /**
     * @return iterable<string, array{list<bool>, array}>
     */
    public static function providerGetValues(): iterable
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
        return array_map($this->exportValue(...), $values);
    }
}
