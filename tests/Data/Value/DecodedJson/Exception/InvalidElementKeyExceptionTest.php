<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidElementKeyException;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidElementKeyException
 */
class InvalidElementKeyExceptionTest extends TestCase
{

    /**
     * @param $key
     * @param array $pathElements
     * @param $expectedValue
     * @dataProvider providerKeyMessage
     */
    public function testGetMessage_Constructed_ReturnsMatchingValue($key, array $pathElements, $expectedValue): void
    {
        $exception = new InvalidElementKeyException($key, new Path(...$pathElements));
        self::assertSame($expectedValue, $exception->getMessage());
    }

    public function providerKeyMessage(): array
    {
        /** @noinspection HtmlUnknownTag */
        return [
            'Float key with empty path' => [0.5, [], 'Invalid element key in decoded JSON: <double> at /'],
            'Integer key with empty path' => [1, [], 'Invalid element key in decoded JSON: 1 at /'],
            'String key with non-empty path' => ['a', ['b', 1], 'Invalid element key in decoded JSON: a at /b/1'],
        ];
    }

    public function testGetPath_ConstructedWithGivenPath_ReturnsSameInstance(): void
    {
        $path = new Path;
        $exception = new InvalidElementKeyException(0, $path);
        self::assertSame($path, $exception->getPath());
    }

    /**
     * @param $key
     * @param $expectedValue
     * @dataProvider providerKey
     */
    public function testGetKey_ConstructedWithGivenKey_ReturnsSameValue($key, $expectedValue): void
    {
        $exception = new InvalidElementKeyException($key, new Path);
        self::assertSame($expectedValue, $exception->getKey());
    }

    public function providerKey(): array
    {
        return [
            'Integer' => [1, 1],
            'String' => ['a', 'a'],
        ];
    }

    public function testGetCode_Always_ReturnZero(): void
    {
        $exception = new InvalidElementKeyException(0, new Path);
        self::assertSame(0, $exception->getCode());
    }

    public function testGetPrevious_ConstructedWithoutPrevious_ReturnsNull(): void
    {
        $exception = new InvalidElementKeyException(0, new Path);
        self::assertNull($exception->getPrevious());
    }

    public function testGetPrevious_ConstructedWithGivenPrevious_ReturnsSameInstance(): void
    {
        $previous = new Exception;
        $exception = new InvalidElementKeyException(0, new Path, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
