<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\ElementEvent;
use Remorhaz\JSON\Data\Path;

/**
 * @covers \Remorhaz\JSON\Data\Event\ElementEvent
 */
class ElementEventTest extends TestCase
{

    public function testGetPath_ConstructedWithGivenPathInstance_ReturnsSameInstance(): void
    {
        $path = new Path;
        $event = new ElementEvent(0, $path);
        self::assertSame($path, $event->getPath());
    }

    public function testGetIndex_ConstructedWithGivenIndex_ReturnsSameValue(): void
    {
        $event = new ElementEvent(1, new Path);
        self::assertSame(1, $event->getIndex());
    }
}
