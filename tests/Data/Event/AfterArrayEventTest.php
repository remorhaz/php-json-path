<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\ArrayValueInterface;
use Remorhaz\JSON\Data\Event\AfterArrayEvent;

/**
 * @covers \Remorhaz\JSON\Data\Event\AfterArrayEvent
 */
class AfterArrayEventTest extends TestCase
{

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ArrayValueInterface::class);
        $event = new AfterArrayEvent($value);
        self::assertSame($value, $event->getValue());
    }
}
