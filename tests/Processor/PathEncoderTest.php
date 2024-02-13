<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\Exception\InvalidPathElementException;
use Remorhaz\JSON\Path\Processor\PathEncoder;

#[CoversClass(PathEncoder::class)]
class PathEncoderTest extends TestCase
{
    /**
     * @param list<mixed> $pathElements
     * @param string      $expectedValue
     */
    #[DataProvider('providerEncodePath')]
    public function testEncodePath_ConstructedWithGivenElements_ReturnsMatchingValue(
        array $pathElements,
        string $expectedValue,
    ): void {
        $path = new Path(...$pathElements);
        $actualValue = (new PathEncoder())->encodePath($path);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{list<mixed>, string}>
     */
    public static function providerEncodePath(): iterable
    {
        return [
            'Empty path' => [[], '$'],
            'Single element' => [[1], "$[1]"],
            'Single latin property' => [['a'], "$['a']"],
            'Single cyrillic property' => [['ж'], "\$['ж']"],
            'Single property with single quote' => [['\''], "\$['\\'']"],
            'Single property with slash' => [['\\'], "\$['\\\\']"],
            'Property in element' => [[1, 'a'], "\$[1]['a']"],
        ];
    }

    public function testEncodePath_PathContainsInvalidElement_ThrowsException(): void
    {
        $path = $this->createMock(PathInterface::class);
        $path
            ->method('getElements')
            ->willReturn([null]);
        $encoder = new PathEncoder();

        $this->expectException(InvalidPathElementException::class);
        $encoder->encodePath($path);
    }
}
