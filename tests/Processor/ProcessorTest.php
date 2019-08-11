<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Processor\Exception\IndefiniteQueryException;
use Remorhaz\JSON\Path\Processor\Exception\QueryNotAddressableException;
use Remorhaz\JSON\Path\Processor\Mutator\Exception\ReplaceAtNestedPathsException;
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
     * @dataProvider providerSelect
     */
    public function testSelect_GivenQueryAndData_ReturnsMatchingData(
        string $json,
        string $path,
        array $expectedValue
    ): void {
        $actualValue = Processor::create()->select(
            QueryFactory::create()->createQuery($path),
            NodeValueFactory::create()->createValue($json)
        );
        self::assertSame($expectedValue, $actualValue->encode());
    }

    public function providerSelect(): array
    {
        return [
            'Query matches nothing' => ['{}', '$.a', []],
            'Query matches single value' => ['{"a":1}', '$.a', ['1']],
            'Query matches several values' => [
                '{"a":{"a":1}}',
                '$..a',
                ['{"a":1}', '1'],
            ],
        ];
    }

    /**
     * @param string $json
     * @param string $path
     * @param array $expectedValue
     * @dataProvider providerSelectPaths
     */
    public function testSelectPaths_GivenQueryAndData_ResultContainsMatchingPaths(
        string $json,
        string $path,
        array $expectedValue
    ): void {
        $actualValue = Processor::create()->selectPaths(
            QueryFactory::create()->createQuery($path),
            NodeValueFactory::create()->createValue($json)
        );
        self::assertSame($expectedValue, $actualValue->encode());
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

    public function testSelectOne_DefiniteQueryMatchesData_ResultContainsMatchingData(): void
    {
        $actualData = Processor::create()->selectOne(
            QueryFactory::create()->createQuery('$.a'),
            NodeValueFactory::create()->createValue('{"a":1}')
        );
        self::assertSame('1', $actualData->encode());
    }

    public function testSelectOne_DefiniteQueryMatchesNothing_ResultNotExists(): void
    {
        $actualData = Processor::create()->selectOne(
            QueryFactory::create()->createQuery('$.a'),
            NodeValueFactory::create()->createValue('{}')
        );
        self::assertFalse($actualData->exists());
    }

    public function testSelectOne_IndefiniteQuery_ThrowsException(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$..a');
        $rootValue = NodeValueFactory::create()->createValue('{"a":1}');

        $this->expectException(IndefiniteQueryException::class);
        $processor->selectOne($query, $rootValue);
    }

    public function testSelectOnePath_DefinitePathQueryMatchesData_ResultContainsMatchingData(): void
    {
        $actualData = Processor::create()->selectOnePath(
            QueryFactory::create()->createQuery('$.a'),
            NodeValueFactory::create()->createValue('{"a":1}')
        );
        self::assertSame("\$['a']", $actualData->encode());
    }

    public function testSelectOnePath_DefinitePathQueryMatchesNothing_ResultNotExists(): void
    {
        $actualData = Processor::create()->selectOnePath(
            QueryFactory::create()->createQuery('$.a'),
            NodeValueFactory::create()->createValue('{}')
        );
        self::assertFalse($actualData->exists());
    }

    public function testSelectOnePath_DefiniteNonPathQuery_ThrowsException(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$.a.length()');
        $rootValue = NodeValueFactory::create()->createValue('{"a":1}');

        $this->expectException(QueryNotAddressableException::class);
        $processor->selectOnePath($query, $rootValue);
    }

    public function testSelectOnePath_IndefiniteQuery_ThrowsException(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$..a');
        $rootValue = NodeValueFactory::create()->createValue('{"a":1}');

        $this->expectException(IndefiniteQueryException::class);
        $processor->selectOnePath($query, $rootValue);
    }

    public function testDelete_NonAddressableQuery_ThrowsException(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$.length()');
        $rootValue = NodeValueFactory::create()->createValue('[]');

        $this->expectException(QueryNotAddressableException::class);
        $processor->delete($query, $rootValue);
    }

    public function testDelete_NonRootAddressableQuery_ResultContainsMatchingData(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$..a');
        $rootValue = NodeValueFactory::create()->createValue('{"a":1,"b":{"a":2,"c":3}}');

        $result = $processor->delete($query, $rootValue);
        self::assertSame('{"b":{"c":3}}', $result->encode());
    }

    public function testDelete_RootQuery_ResultNotExists(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$');
        $rootValue = NodeValueFactory::create()->createValue('{}');

        $result = $processor->delete($query, $rootValue);
        self::assertFalse($result->exists());
    }

    public function testReplace_AddressableQueryWithoutNestedPaths_ResultContainsMatchingData(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$..a');
        $valueFactory = NodeValueFactory::create();
        $rootValue = $valueFactory->createValue('{"a":1,"b":{"a":2,"c":3}}');
        $newValue = $valueFactory->createValue('{"b":4}');

        $result = $processor->replace($query, $rootValue, $newValue);
        self::assertSame('{"a":{"b":4},"b":{"a":{"b":4},"c":3}}', $result->encode());
    }

    public function testReplace_AddressableQueryWithNestedPaths_ThrowsException(): void
    {
        $processor = Processor::create();
        $query = QueryFactory::create()->createQuery('$..a');
        $valueFactory = NodeValueFactory::create();
        $rootValue = $valueFactory->createValue('{"a":{"a":2,"c":3}}');
        $newValue = $valueFactory->createValue('{"b":4}');

        $this->expectException(ReplaceAtNestedPathsException::class);
        $processor->replace($query, $rootValue, $newValue);
    }
}
