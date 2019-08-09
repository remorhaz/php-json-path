<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson;

use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidElementKeyException;
use Remorhaz\JSON\Data\Path\Path;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue
 */
class NodeArrayValueTest extends TestCase
{

    /**
     * @param array $data
     * @dataProvider providerArrayWithInvalidIndex
     */
    public function testCreateChildIterator_ArrayDataWithInvalidIndex_ThrowsException(array $data): void
    {
        $value = new NodeArrayValue(
            $data,
            new Path,
            new NodeValueFactory,
        );

        $this->expectException(InvalidElementKeyException::class);
        iterator_to_array($value->createChildIterator());
    }

    public function providerArrayWithInvalidIndex(): array
    {
        return [
            'Non-zero first index' => [[1 => 'a']],
            'Non-integer first index' => [['a' => 'b']],
        ];
    }
}
