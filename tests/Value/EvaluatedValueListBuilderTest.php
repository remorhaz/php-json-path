<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Value\EvaluatedValueListBuilder;

#[CoversClass(EvaluatedValueListBuilder::class)]
class EvaluatedValueListBuilderTest extends TestCase
{
    public function testBuild_NoResultsAdded_ReturnsListWithEmptyIndexMap(): void
    {
        $builder = new EvaluatedValueListBuilder();
        $values = $builder->build();
        self::assertSame([], $values->getIndexMap()->getOuterIndexes());
    }

    public function testBuild_NoResultsAdded_ReturnsEmptyList(): void
    {
        $builder = new EvaluatedValueListBuilder();
        $values = $builder->build();
        self::assertSame([], $values->getValues());
    }

    /**
     * @param bool       $result
     * @param list<bool> $expectedValues
     */
    #[DataProvider('providerResultAdded')]
    public function testBuild_ResultAdded_ReturnsListWithSameResult(bool $result, array $expectedValues): void
    {
        $builder = new EvaluatedValueListBuilder();
        $builder->addResult($result, 1);
        $values = $builder->build();
        self::assertSame($expectedValues, $values->getResults());
    }

    /**
     * @return iterable<string, array{bool, list<bool>}>
     */
    public static function providerResultAdded(): iterable
    {
        return [
            'False' => [false, [false]],
            'True' => [true, [true]],
        ];
    }

    public function testBuild_ValuesAdded_ReturnsListWithSameValueInstances(): void
    {
        $builder = new EvaluatedValueListBuilder();
        $builder->addResult(true, 1);
        $builder->addResult(false, 1);
        $values = $builder->build();
        self::assertSame([true, false], $values->getResults());
    }

    public function testBuild_ValueAddedWithGivenOuterIndex_ReturnsListWithSameOuterIndexInMap(): void
    {
        $builder = new EvaluatedValueListBuilder();
        $builder->addResult(true, 1);
        $values = $builder->build();
        self::assertSame([1], $values->getIndexMap()->getOuterIndexes());
    }

    public function testBuild_ValuesAddedWithGivenOuterIndex_ReturnsListWithSameOuterIndexesInMap(): void
    {
        $builder = new EvaluatedValueListBuilder();
        $builder->addResult(true, 1);
        $builder->addResult(false, 2);
        $values = $builder->build();
        self::assertSame([1, 2], $values->getIndexMap()->getOuterIndexes());
    }

    public function testAddResult_Constructed_ReturnsSelf(): void
    {
        $builder = new EvaluatedValueListBuilder();
        self::assertSame($builder, $builder->addResult(true, 1));
    }
}
