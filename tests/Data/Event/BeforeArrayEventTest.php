<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Event\BeforeArrayEvent;

/**
 * @covers \Remorhaz\JSON\Data\Event\BeforeArrayEvent
 */
class BeforeArrayEventTest extends TestCase
{

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ArrayValueInterface::class);
        $event = new BeforeArrayEvent($value);
        self::assertSame($value, $event->getValue());
    }
}
