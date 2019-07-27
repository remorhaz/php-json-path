<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Processor
 */
class ProcessorTest extends TestCase
{

    /**
     * @param string $json
     * @param string $path
     * @param array $expectedValue
     * @dataProvider providerSelectPaths
     */
    public function testSelectPaths_GivenQueryAndData_ReturnsMatchingPaths(
        string $json,
        string $path,
        array $expectedValue
    ): void {
        $actualValue = Processor::create()->selectPaths(
            QueryFactory::create()->createQuery($path),
            NodeValueFactory::create()->createValue($json)
        );
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerSelectPaths(): array
    {
        return [
            'Query matches nothing' => ['{}', '$.a', []],
            'Query matches single value' => ['{"a":1}', '$.a',["\$['a']"]],
            'Query matches several values' => [
                '{"a":{"a":1}}',
                '$..a',
                [
                    "\$['a']",
                    "\$['a']['a']",
                ]
            ],
        ];
    }
}
