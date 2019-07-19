<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\ScalarEvent;
use Remorhaz\JSON\Data\ScalarValueInterface;

/**
 * @covers \Remorhaz\JSON\Data\Event\ScalarEvent
 */
class ScalarEventTest extends TestCase
{

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ScalarValueInterface::class);
        $event = new ScalarEvent($value);
        self::assertSame($value, $event->getValue());
    }
}
