<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectPathsResult;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\SelectPathsResult
 */
class SelectPathsResultTest extends TestCase
{

    public function testGet_ConstructedWithoutPaths_ReturnsEmptyArray(): void
    {
        $result = new SelectPathsResult(
            $this->createMock(PathEncoderInterface::class)
        );
        self::assertSame([], $result->get());
    }

    public function testGet_ConstructedWithPath_ReturnsSameInstanceInArray(): void
    {
        $path = $this->createMock(PathInterface::class);
        $result = new SelectPathsResult(
            $this->createMock(PathEncoderInterface::class),
            $path
        );
        self::assertSame([$path], $result->get());
    }

    public function testEncode_ConstructedWithoutPath_ReturnsEmptyArray(): void
    {
        $result = new SelectPathsResult(
            $this->createMock(PathEncoderInterface::class)
        );
        self::assertSame([], $result->encode());
    }

    public function testEncode_ConstructedWithPath_PassesPathToEncoder(): void
    {
        $encoder = $this->createMock(PathEncoderInterface::class);
        $path = $this->createMock(PathInterface::class);
        $result = new SelectPathsResult($encoder, $path);

        $encoder
            ->expects(self::once())
            ->method('encodePath')
            ->with(self::identicalTo($path));
        $result->encode();
    }

    public function testEncode_EncoderReturnsValue_ReturnsSameValueInArray(): void
    {
        $encoder = $this->createMock(PathEncoderInterface::class);
        $path = $this->createMock(PathInterface::class);
        $result = new SelectPathsResult($encoder, $path);

        $encoder
            ->method('encodePath')
            ->willReturn('a');
        self::assertSame(['a'], $result->encode());
    }
}
