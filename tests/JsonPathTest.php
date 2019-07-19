<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test;

use function file_get_contents;
use function json_decode;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\JsonFactory;
use Remorhaz\JSON\Path\JsonPath;

/**
 * @coversNothing
 */
class JsonPathTest extends TestCase
{

    private $example;

    public function setUp(): void
    {
        $this->example = json_decode(file_get_contents(__DIR__ . '/goessner.json'));
    }

    /**
     * @param string $path
     * @param array $expectedValue
     * @dataProvider providerGoessnerExamples
     */
    public function testSelect_GoessnerExamples_WorkAsExpected(string $path, array $expectedValue): void
    {
        $jsonPath = JsonPath::create();
        $result = $jsonPath->select(
            $jsonPath->createQuery($path),
            JsonFactory::create()->fromDecodedJson($this->example)
        );

        self::assertEquals($expectedValue, $result->asJson());
    }

    public function providerGoessnerExamples(): array
    {
        return [
            'the authors of all books in the store' => [
                '$.store.book[*].author',
                [
                    '"Nigel Rees"',
                    '"Evelyn Waugh"',
                    '"Herman Melville"',
                    '"J. R. R. Tolkien"',
                ],
            ],
            'all authors' => [
                '$..author',
                [
                    '"Nigel Rees"',
                    '"Evelyn Waugh"',
                    '"Herman Melville"',
                    '"J. R. R. Tolkien"',
                ],
            ],
            'all things in store, which are some books and a red bicycle' => [
                '$.store.*',
                [
                    '[' .
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95},' .
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99},' .
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99},' .
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}' .
                    ']',
                    '{"color":"red","price":19.95}',
                ],
            ],
            'the price of everything in the store' => [
                '$.store..price',
                [
                    '8.95',
                    '12.99',
                    '8.99',
                    '22.99',
                    '19.95',
                ]
            ],
            'the third book' => [
                '$..book[2]',
                [
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99}',
                ],
            ],
            // the last book in order (1) is skipped (JS expressions not supported): $..book[(@.length-1)
            'the last book in order (2)' => [
                '$..book[-1:]',
                [
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}',
                ],
            ],
            'the first two books (1)' => [
                '$..book[0,1]',
                [
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95}',
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99}',
                ],
            ],
            'the first two books (2)' => [
                '$..book[:2]',
                [
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95}',
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99}',
                ],
            ],
            'filter all books with isbn number' => [
                '$..book[?(@.isbn)]',
                [
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99}',
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}',
                ],
            ],
            'filter all books cheapier than 10' => [
                '$..book[?(@.price<10)]',
                [
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95}',
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99}',
                ],
            ],
            'All members of JSON structure' => [
                '$..*',
                [
                    '{"book":[' .
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95},' .
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99},' .
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99},' .
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}' .
                    '],"bicycle":{"color":"red","price":19.95}}',
                    '[' .
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95},' .
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99},' .
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99},' .
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}' .
                    ']',
                    '{"category":"reference","author":"Nigel Rees","title":"Sayings of the Century","price":8.95}',
                    '"reference"',
                    '"Nigel Rees"',
                    '"Sayings of the Century"',
                    '8.95',
                    '{"category":"fiction","author":"Evelyn Waugh","title":"Sword of Honour","price":12.99}',
                    '"fiction"',
                    '"Evelyn Waugh"',
                    '"Sword of Honour"',
                    '12.99',
                    '{"category":"fiction","author":"Herman Melville","title":"Moby Dick",' .
                    '"isbn":"0-553-21311-3","price":8.99}',
                    '"fiction"',
                    '"Herman Melville"',
                    '"Moby Dick"',
                    '"0-553-21311-3"',
                    '8.99',
                    '{"category":"fiction","author":"J. R. R. Tolkien","title":"The Lord of the Rings",' .
                    '"isbn":"0-395-19395-8","price":22.99}',
                    '"fiction"',
                    '"J. R. R. Tolkien"',
                    '"The Lord of the Rings"',
                    '"0-395-19395-8"',
                    '22.99',
                    '{"color":"red","price":19.95}',
                    '"red"',
                    '19.95',
                    '10',
                ]
            ],
        ];
    }
}
