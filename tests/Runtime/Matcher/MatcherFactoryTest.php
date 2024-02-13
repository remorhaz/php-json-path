<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactoryInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Runtime\Matcher\MatcherFactory;

#[CoversClass(MatcherFactory::class)]
class MatcherFactoryTest extends TestCase
{
    public function testMatchAnyChild_Constructed_ReturnsAnyChildMatcherInstance(): void
    {
        $factory = new MatcherFactory();
        self::assertInstanceOf(AnyChildMatcher::class, $factory->matchAnyChild());
    }

    public function testMatchPropertyStrictly_PropertyList_NonExistingPropertyNotMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchPropertyStrictly('a')
            ->match(
                'b',
                $this->createMock(NodeValueInterface::class),
                $this->createMock(NodeValueInterface::class),
            );
        self::assertFalse($actualValue);
    }

    public function testMatchPropertyStrictly_PropertyList_ExistingPropertyMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchPropertyStrictly('a')
            ->match(
                'a',
                $this->createMock(NodeValueInterface::class),
                $this->createMock(NodeValueInterface::class),
            );
        self::assertTrue($actualValue);
    }

    public function testMatchElementStrictly_IndexList_NonExistingIndexNotMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchElementStrictly(1)
            ->match(
                2,
                $this->createMock(NodeValueInterface::class),
                $this->createMock(NodeValueInterface::class),
            );
        self::assertFalse($actualValue);
    }

    public function testMatchElementStrictly_IndexList_ExistingIndexMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchElementStrictly(1)
            ->match(
                1,
                $this->createMock(NodeValueInterface::class),
                $this->createMock(NodeValueInterface::class),
            );
        self::assertTrue($actualValue);
    }

    public function testMatchElementSlice_Slice_NonMatchingIndexNotMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchElementSlice(0, 2, 2)
            ->match(
                1,
                $this->createMock(NodeValueInterface::class),
                new NodeArrayValue(
                    ['a', 'b'],
                    $this->createMock(PathInterface::class),
                    $this->createMock(NodeValueFactoryInterface::class),
                ),
            );
        self::assertFalse($actualValue);
    }

    public function testMatchElementSlice_Slice_MatchingIndexMatches(): void
    {
        $factory = new MatcherFactory();
        $actualValue = $factory
            ->matchElementSlice(0, 2, 2)
            ->match(
                0,
                $this->createMock(NodeValueInterface::class),
                new NodeArrayValue(
                    ['a', 'b'],
                    $this->createMock(PathInterface::class),
                    $this->createMock(NodeValueFactoryInterface::class),
                ),
            );
        self::assertTrue($actualValue);
    }
}
