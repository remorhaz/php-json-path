<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Runtime\Matcher;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\AnyChildMatcher;

#[CoversClass(AnyChildMatcher::class)]
class AnyChildMatcherTest extends TestCase
{
    public function testMatch_Always_ReturnsTrue(): void
    {
        $matcher = new AnyChildMatcher();
        $actualValue = $matcher->match(
            '',
            $this->createMock(NodeValueInterface::class),
            $this->createMock(NodeValueInterface::class),
        );
        self::assertTrue($actualValue);
    }
}
