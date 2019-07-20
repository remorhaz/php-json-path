<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidNodeDataException;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidNodeDataException
 */
class InvalidNodeDataExceptionTest extends TestCase
{

    /**
     * @param array $elements
     * @param $expectedValue
     * @dataProvider providerGetMessage
     */
    public function testGetMessage_Constructed_ReturnsMatchingValue(array $elements, $expectedValue): void
    {
        $exception = new InvalidNodeDataException(null, new Path(...$elements));
        self::assertSame($expectedValue, $exception->getMessage());
    }

    public function providerGetMessage(): array
    {
        return [
            'Empty path' => [[], 'Invalid data in decoded JSON at /'],
            'Non-empty path' => [['a', 1], 'Invalid data in decoded JSON at /a/1'],
        ];
    }

    public function testGetData_ConstructedWithGivenData_ReturnsSameInstance(): void
    {
        $data = (object) [];
        $exception = new InvalidNodeDataException($data, new Path);
        self::assertSame($data, $exception->getData());
    }

    public function testGetPath_ConstructedWithGivenPath_ReturnsSameInstance(): void
    {
        $path = new Path;
        $exception = new InvalidNodeDataException(0, $path);
        self::assertSame($path, $exception->getPath());
    }

    public function testGetCode_Always_ReturnZero(): void
    {
        $exception = new InvalidNodeDataException(0, new Path);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidNodeDataException(0, new Path);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new InvalidNodeDataException(0, new Path, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
