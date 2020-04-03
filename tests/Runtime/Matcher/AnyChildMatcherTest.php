<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\AnyChildMatcher;

/**
 * @covers \Remorhaz\JSON\Path\Runtime\Matcher\AnyChildMatcher
 */
class AnyChildMatcherTest extends TestCase
{

    public function testMatch_Always_ReturnsTrue(): void
    {
        $matcher = new AnyChildMatcher();
        $actualValue = $matcher->match(
            '',
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class)
        );
        self::assertTrue($actualValue);
    }
}
