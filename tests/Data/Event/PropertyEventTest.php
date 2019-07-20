<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Event;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\PropertyEvent;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Event\PropertyEvent
 */
class PropertyEventTest extends TestCase
{

    public function testGetPath_ConstructedWithGivenPathInstance_ReturnsSameInstance(): void
    {
        $path = new Path;
        $event = new PropertyEvent('a', $path);
        self::assertSame($path, $event->getPath());
    }

    public function testGetIndex_ConstructedWithGivenIndex_ReturnsSameValue(): void
    {
        $event = new PropertyEvent('a', new Path);
        self::assertSame('a', $event->getName());
    }
}
