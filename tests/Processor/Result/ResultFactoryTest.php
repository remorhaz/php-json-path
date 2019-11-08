<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;
use Remorhaz\JSON\Path\Processor\Result\Exception\MoreThanOneValueInListException;
use Remorhaz\JSON\Path\Processor\Result\Exception\PathNotFoundInValueException;
use Remorhaz\JSON\Path\Processor\Result\ResultFactory;
use Remorhaz\JSON\Path\Value\ValueListInterface;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\ResultFactory
 */
class ResultFactoryTest extends TestCase
{

    public function testCreateSelectResult_NoValuesInList_ResultEncodesToEmptyArray(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $result = $factory->createSelectResult(
            $this->createMock(ValueListInterface::class)
        );
        self::assertSame([], $result->encode());
    }

    public function testCreateSelectResult_GivenValueInList_ResultPassesSameValueToEncoder(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $factory = new ResultFactory(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $result = $factory->createSelectResult($values);

        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testCreateSelectResult_GivenValue_ResultPassesSameValueToDecoder(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $result = $factory->createSelectResult($values);

        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }

    public function testCreateSelectOneResult_NoValuesInList_ResultNotExists(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([]);
        $result = $factory->createSelectOneResult($values);
        self::assertFalse($result->exists());
    }

    public function testCreateSelectOneResult_MoreThanOneValueInList_ThrowsException(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value, $value]);
        $this->expectException(MoreThanOneValueInListException::class);
        $factory->createSelectOneResult($values);
    }

    public function testCreateSelectOneResult_ExactlyOneValueInList_ResultExists(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $factory = new ResultFactory(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getValue')
            ->with(0)
            ->willReturn($value);
        $result = $factory->createSelectOneResult($values);
        self::assertTrue($result->exists());
    }

    public function testCreateSelectOneResult_ExactlyOneValueInList_ResultPassesSameInstanceToEncoder(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $factory = new ResultFactory(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getValue')
            ->with(0)
            ->willReturn($value);
        $result = $factory->createSelectOneResult($values);
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testCreateSelectOneResult_ExactlyOneValueInList_ResultPassesSameInstanceToDecoder(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getValue')
            ->with(0)
            ->willReturn($value);
        $result = $factory->createSelectOneResult($values);
        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }

    public function testCreateSelectPathResult_ValueWithoutPathInList_ThrowsException(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$this->createMock(ValueInterface::class)]);
        $this->expectException(PathNotFoundInValueException::class);
        $factory->createSelectPathsResult($values);
    }

    public function testCreateSelectPathResult_ValueWithPathInList_ResultPassesSameInstanceToPathEncoderOnEncode(): void
    {
        $pathEncoder = $this->createMock(PathEncoderInterface::class);
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $pathEncoder
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $path = $this->createMock(PathInterface::class);
        $value
            ->method('getPath')
            ->willReturn($path);
        $result = $factory->createSelectPathsResult($values);
        $pathEncoder
            ->expects(self::once())
            ->method('encodePath')
            ->with(self::identicalTo($path));
        $result->encode();
    }

    public function testCreateSelectOnePathResult_NoValuesInList_ResultNotExists(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $values
            ->method('getValues')
            ->willReturn([]);
        $result = $factory->createSelectOnePathResult($values);
        self::assertFalse($result->exists());
    }

    public function testCreateSelectOnePathResult_MoreThanOneValueInListThrowsException(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value, $value]);
        $this->expectException(MoreThanOneValueInListException::class);
        $factory->createSelectOnePathResult($values);
    }

    public function testCreateSelectOnePathResult_ValueWithoutPathInListThrowsException(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(ValueInterface::class);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $this->expectException(PathNotFoundInValueException::class);
        $factory->createSelectOnePathResult($values);
    }

    public function testCreateSelectOnePathResult_ExactlyOneValueWithPathInList_ResultHasSamePath(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $path = $this->createMock(PathInterface::class);
        $value
            ->method('getPath')
            ->willReturn($path);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getValue')
            ->with(0)
            ->willReturn($value);
        $result = $factory->createSelectOnePathResult($values);
        self::assertSame($path, $result->get());
    }

    public function testCreateSelectOnePathResult_ExactlyOneValueWithPathInList_ResultEncodesSamePath(): void
    {
        $pathEncoder = $this->createMock(PathEncoderInterface::class);
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $pathEncoder
        );
        $values = $this->createMock(ValueListInterface::class);
        $value = $this->createMock(NodeValueInterface::class);
        $path = $this->createMock(PathInterface::class);
        $value
            ->method('getPath')
            ->willReturn($path);
        $values
            ->method('getValues')
            ->willReturn([$value]);
        $values
            ->method('getValue')
            ->with(0)
            ->willReturn($value);
        $result = $factory->createSelectOnePathResult($values);

        $pathEncoder
            ->expects(self::once())
            ->method('encodePath')
            ->with(self::identicalTo($path));
        $result->encode();
    }

    public function testCreateValueResult_NoValue_ResultNotExists(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $result = $factory->createValueResult(null);
        self::assertFalse($result->exists());
    }

    public function testCreateValueResult_GivenValue_ResultExists(): void
    {
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $result = $factory->createValueResult(
            $this->createMock(ValueInterface::class)
        );
        self::assertTrue($result->exists());
    }

    public function testCreateValueResult_GivenValue_ResultPassesSameInstanceToEncoderOnEncode(): void
    {
        $encoder = $this->createMock(ValueEncoderInterface::class);
        $factory = new ResultFactory(
            $encoder,
            $this->createMock(ValueDecoderInterface::class),
            $this->createMock(PathEncoderInterface::class)
        );
        $value = $this->createMock(ValueInterface::class);
        $result = $factory->createValueResult($value);
        $encoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->encode();
    }

    public function testCreateValueResult_GivenValue_ResultPassesSameInstanceToDecoderOnDecode(): void
    {
        $decoder = $this->createMock(ValueDecoderInterface::class);
        $factory = new ResultFactory(
            $this->createMock(ValueEncoderInterface::class),
            $decoder,
            $this->createMock(PathEncoderInterface::class)
        );
        $value = $this->createMock(ValueInterface::class);
        $result = $factory->createValueResult($value);
        $decoder
            ->expects(self::once())
            ->method('exportValue')
            ->with(self::identicalTo($value));
        $result->decode();
    }
}
