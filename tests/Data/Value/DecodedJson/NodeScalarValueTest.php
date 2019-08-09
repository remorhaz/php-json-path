<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidNodeDataException;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\NodeScalarValue
 */
class NodeScalarValueTest extends TestCase
{

    /**
     * @param $data
     * @dataProvider providerInvalidData
     */
    public function testConstruct_InvalidData_ThrowsMatchingException($data): void
    {
        $this->expectException(InvalidNodeDataException::class);
        new NodeScalarValue($data, new Path);
    }

    public function providerInvalidData(): array
    {
        return [
            'Resource' => [STDERR],
            'Invalid object' => [new class {
            }],
            'Array' => [[]],
            'Object' => [(object) []],
        ];
    }
}
