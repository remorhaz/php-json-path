<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectResult;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\SelectResult
 */
class SelectResultTest extends TestCase
{

    public function testEncode_ConstructedWithoutValues_ReturnsEmptyArray(): void
    {
        $result = new SelectResult(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class)
        );
        self::assertSame([], $result->encode());
    }

    public function testEncode_ConstructedWithValue_PassesSameInstanceToEncoder(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new SelectResult(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $value
        );

        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testDecode_ConstructedWithValue_PassesSameInstanceToDecoder(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new SelectResult(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $value
        );

        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }

    public function testEncode_EncoderReturnsValue_ReturnsSameValueInArray(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $result = new SelectResult(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(ValueInterface::class)
        );

        $encoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame(['a'], $result->encode());
    }

    public function testDecode_DecoderReturnsValue_ReturnsSameValueInArray(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $result = new SelectResult(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $this->createMock(ValueInterface::class)
        );

        $decoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame(['a'], $result->decode());
    }
}
