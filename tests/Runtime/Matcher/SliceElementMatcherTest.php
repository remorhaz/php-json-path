<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactoryInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\SliceElementMatcher;

use function array_fill;

/**
 * @covers  \Remorhaz\JSON\Path\Runtime\Matcher\SliceElementMatcher
 */
class SliceElementMatcherTest extends TestCase
{

    public function testMatch_MatchingIndexNonArrayContainer_ReturnsFalse(): void
    {
        $matcher = new SliceElementMatcher(0, 2, 1);
        $actualValue = $matcher->match(
            0,
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertFalse($actualValue);
    }

    /**
     * @param int|null $start
     * @param int|null $end
     * @param int|null $step
     * @param          $address
     * @param          $elementCount
     * @dataProvider providerNonMatchingIndex
     */
    public function testMatch_NonMatchingIndexArrayContainer_ReturnsFalse(
        ?int $start,
        ?int $end,
        ?int $step,
        $address,
        $elementCount
    ): void {
        $matcher = new SliceElementMatcher($start, $end, $step);
        $actualValue = $matcher->match(
            $address,
            $this->createMock(NodeValueInterface::class),
            $this->createArrayValueOfLength($elementCount)
        );
        self::assertFalse($actualValue);
    }

    public function providerNonMatchingIndex(): array
    {
        return [
            'Zero step, int address, non-empty container' => [null, null, 0, 1, 2],
            'Positive step, non-int address, non-empty container' => [null, null, 1, 'a', 2],
            'Negative step, non-int address, non-empty container' => [null, null, -1, 'a', 2],
            'Positive step, int address, empty container' => [null, null, 1, 1, 0],
            'Index before container start (straight range)' => [null, null, 1, -1, 2],
            'Index before container start (reverse range)' => [null, null, -1, -1, 2],
            'Index before container start (explicit matcher start, straight range)' => [0, null, 1, -1, 2],
            'Index after container end (straight range)' => [null, null, 1, 2, 2],
            'Index after container end (reverse range)' => [null, null, -1, 2, 2],
            'Index after container end (explicit matcher end, straight range)' => [null, 2, 1, 2, 2],
            'Index after container end (explicit matcher start, reverse range)' => [1, null, -1, 2, 2],
            'Index in container range but before matcher start (straight range)' => [1, null, 1, 0, 2],
            'Index in matcher range but before matcher end (straight range)' => [1, 3, 1, 2, 2],
            'Index in container range but at matcher end (reverse range)' => [null, 0, -1, 0, 2],
            'Index at container but at matcher end (straight range)' => [0, 1, 1, 1, 2],
            'Index in range but out of step (straight range)' => [0, 2, 2, 1, 2],
            'Index in range but out of step (reverse range)' => [0, 2, -2, 0, 2],
        ];
    }

    private function createArrayValueOfLength(int $length): NodeValueInterface
    {
        return new NodeArrayValue(
            array_fill(0, $length, null),
            $this->createMock(PathInterface::class),
            $this->createMock(NodeValueFactoryInterface::class)
        );
    }

    /**
     * @param int|null $start
     * @param int|null $end
     * @param int|null $step
     * @param          $address
     * @param          $elementCount
     * @dataProvider providerMatchingIndex
     */
    public function testMatch_MatchingIndex_ReturnsTrue(
        ?int $start,
        ?int $end,
        ?int $step,
        $address,
        $elementCount
    ): void {
        $matcher = new SliceElementMatcher($start, $end, $step);
        $actualValue = $matcher->match(
            $address,
            $this->createMock(NodeValueInterface::class),
            $this->createArrayValueOfLength($elementCount)
        );
        self::assertTrue($actualValue);
    }

    public function providerMatchingIndex(): array
    {
        return [
            'At container start (straight range)' => [null, null, 1, 0, 2],
            'At container start (reverse range)' => [null, null, -1, 0, 2],
            'At container end (straight range)' => [null, null, 1, 1, 2],
            'At container end (reverse range)' => [null, null, -1, 1, 2],
            'At matcher start (straight range)' => [1, null, 1, 1, 2],
            'At matcher start (reverse range)' => [0, null, -1, 0, 2],
            'Before matcher end (straight range)' => [null, 1, 1, 0, 2],
            'Before matcher end (reverse range)' => [null, 0, -1, 1, 2],
            'Index in range and in step (straight range)' => [null, null, 2, 0, 2],
            'Index in range but out of step (reverse range)' => [null, null, -2, 1, 2],
        ];
    }
}
