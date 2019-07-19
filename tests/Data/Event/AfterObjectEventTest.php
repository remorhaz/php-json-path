<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\AfterObjectEvent;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;

/**
 * @covers \Remorhaz\JSON\Data\Event\AfterObjectEvent
 */
class AfterObjectEventTest extends TestCase
{

    public function testGetValue_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ObjectValueInterface::class);
        $event = new AfterObjectEvent($value);
        self::assertSame($value, $event->getValue());
    }
}
