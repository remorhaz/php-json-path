<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;
use Remorhaz\JSON\Path\Processor\Result\ExistingSelectOnePathResult;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\ExistingSelectOnePathResult
 */
class ExistingSelectOnePathResultTest extends TestCase
{

    public function testExists_Always_ReturnsTrue(): void
    {
        $result = new ExistingSelectOnePathResult(
            $this->createMock(PathEncoderInterface::class),
            $this->createMock(PathInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testGet_ConstructedWithPath_ReturnsSameInstance(): void
    {
        $path = $this->createMock(PathInterface::class);
        $result = new ExistingSelectOnePathResult(
            $this->createMock(PathEncoderInterface::class),
            $path
        );
        self::assertSame($path, $result->get());
    }

    public function testEncode_Constructed_PassesPathToEncoder(): void
    {
        $encoder = $this->createMock(PathEncoderInterface::class);
        $path = $this->createMock(PathInterface::class);
        $result = new ExistingSelectOnePathResult($encoder, $path);

        $encoder
            ->expects(self::once())
            ->method('encodePath')
            ->with(self::identicalTo($path));
        $result->encode();
    }

    public function testEncode_EncoderResultsValue_ReturnsSameValue(): void
    {
        $encoder = $this->createMock(PathEncoderInterface::class);
        $result = new ExistingSelectOnePathResult(
            $encoder,
            $this->createMock(PathInterface::class)
        );

        $encoder
            ->method('encodePath')
            ->willReturn('a');
        self::assertSame('a', $result->encode());
    }
}
