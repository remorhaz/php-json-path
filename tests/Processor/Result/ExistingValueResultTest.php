<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Processor\Result\ExistingValueResult;

#[CoversClass(ExistingValueResult::class)]
class ExistingValueResultTest extends TestCase
{
    public function testExists_Always_ReturnsTrue(): void
    {
        $result = new ExistingValueResult(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(ValueInterface::class),
        );
        self::assertTrue($result->exists());
    }

    public function testGet_ConstructedWithValue_ReturnsSameInstance(): void
    {
        $value = $this->createMock(ValueInterface::class);
        $result = new ExistingValueResult(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $value,
        );
        self::assertSame($value, $result->get());
    }

    public function testEncode_Constructed_PassesValueToEncoder(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new ExistingValueResult(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $value,
        );
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testEncode_EncoderReturnsValue_ReturnsSameValue(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new ExistingValueResult(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $value,
        );
        $encoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame('a', $result->encode());
    }

    public function testDecode_Constructed_PassesValueToDecoder(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new ExistingValueResult(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $value,
        );
        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }

    public function testDecode_DecoderReturnsValue_ReturnsSameValue(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $result = new ExistingValueResult(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $value,
        );
        $decoder
            ->method('exportValue')
            ->willReturn('a');
        self::assertSame('a', $result->decode());
    }
}
