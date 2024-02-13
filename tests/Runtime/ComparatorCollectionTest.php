<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime;

use Collator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Comparator\EqualValueComparator;
use Remorhaz\JSON\Data\Comparator\GreaterValueComparator;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Path\Runtime\ComparatorCollection;

#[CoversClass(ComparatorCollection::class)]
class ComparatorCollectionTest extends TestCase
{
    public function testEqual_Constructed_ReturnsEqualComparatorInstance(): void
    {
        $comparators = new ComparatorCollection(
            $this->createMock(Collator::class),
        );
        self::assertInstanceOf(EqualValueComparator::class, $comparators->equal());
    }

    public function testEqual_ConstructedWithCollator_ResultUsesSameInstanceOnComparison(): void
    {
        $collator = $this->createMock(Collator::class);
        $comparators = new ComparatorCollection($collator);

        $comparator = $comparators->equal();
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn('a');
        $collator
            ->expects(self::atLeastOnce())
            ->method('compare');
        $comparator->compare($value, $value);
    }

    public function testGreater_Constructed_ReturnsGreaterComparatorInstance(): void
    {
        $comparators = new ComparatorCollection(
            $this->createMock(Collator::class),
        );
        self::assertInstanceOf(GreaterValueComparator::class, $comparators->greater());
    }

    public function testGreater_ConstructedWithCollator_ResultUsesSameInstanceOnComparison(): void
    {
        $collator = $this->createMock(Collator::class);
        $comparators = new ComparatorCollection($collator);

        $comparator = $comparators->greater();
        $value = $this->createMock(ScalarValueInterface::class);
        $value
            ->method('getData')
            ->willReturn('a');
        $collator
            ->expects(self::atLeastOnce())
            ->method('compare');
        $comparator->compare($value, $value);
    }
}
