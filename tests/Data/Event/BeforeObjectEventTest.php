<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\BeforeObjectEvent;
use Remorhaz\JSON\Data\ObjectValueInterface;

/**
 * @covers \Remorhaz\JSON\Data\Event\BeforeObjectEvent
 */
class BeforeObjectEventTest extends TestCase
{

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ObjectValueInterface::class);
        $event = new BeforeObjectEvent($value);
        self::assertSame($value, $event->getValue());
    }
}
