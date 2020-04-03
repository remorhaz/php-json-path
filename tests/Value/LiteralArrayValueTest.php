<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\LiteralArrayValue;

use function iterator_to_array;

/**
 * @covers \Remorhaz\JSON\Path\Value\LiteralArrayValue
 */
class LiteralArrayValueTest extends TestCase
{

    public function testCreateChildIterator_ConstructedWithoutValues_ReturnsEmptyIterator(): void
    {
        $array = new LiteralArrayValue();
        self::assertCount(0, $array->createChildIterator());
    }

    public function testCreateChildIterator_ConstructedWithValue_IteratesSameValueInstance(): void
    {
        $value = $this->createMock(ValueInterface::class);
        $array = new LiteralArrayValue($value);
        self::assertSame([$value], iterator_to_array($array->createChildIterator()));
    }
}
