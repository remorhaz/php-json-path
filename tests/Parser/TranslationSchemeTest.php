<?php

namespace Remorhaz\JSON\Path\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Query\QueryFactory;

/**
 * @covers       \Remorhaz\JSON\Path\Parser\TranslationScheme
 * @todo Maybe it's better to test in isolation, checking the resulting AST.
 */
class TranslationSchemeTest extends TestCase
{

    /**
     * @param string $path
     * @param bool $expectedValue
     * @dataProvider providerIsAddressableCapability
     */
    public function testIsAddressableCapability_GivenQueryParsed_ContainsMatchingValue(
        string $path,
        bool $expectedValue
    ): void {
        $query = QueryFactory::create()->createQuery($path);

        self::assertSame($expectedValue, $query->getCapabilities()->isAddressable());
    }

    public function providerIsAddressableCapability(): array
    {
        return [
            'Dot-notation star' => ['$.*', true],
            'Dot-notation property' => ['$.a', true],
            'Double-dot-notation star' => ['$..*', true],
            'Double-dot-notation property' => ['$..a', true],
            'Aggregate function' => ['$.length()', false],
            'Filter by absolute property' => ['$.a[?($.b)]', true],
            'Filter by relative property' => ['$.a[?(@.b)]', true],
            'Filter by aggregate function' => ['$.a[?(@.b.length() > 1)]', true],
        ];
    }

    /**
     * Because of the scheme complexity it's more convenient to test it in full integration.
     *
     * @param $json
     * @param string $path
     * @param array $expectedValue
     * @param bool $isDefinite
     * @dataProvider providerParser
     */
    public function testTranslationListenerMethods_AssembledWithParser_QueryWorksAsExpected(
        $json,
        string $path,
        array $expectedValue,
        bool $isDefinite
    ): void {
        $query = QueryFactory::create()->createQuery($path);
        // TODO: extract isDefinite test
        self::assertSame($isDefinite, $query->getCapabilities()->isDefinite());

        $result = Processor::create()->select(
            $query,
            NodeValueFactory::create()->createValue($json)
        );

        self::assertEquals($expectedValue, $result->encode());
    }

    public function providerParser(): array
    {
        return [
            'Dot-notation alpha property' => [
                (object) ['a' => true],
                '$.a',
                ['true'],
                true,
            ],
            'Dot-notation numeric property' => [
                (object) ['1' => true],
                '$.1',
                ['true'],
                true,
            ],
            'Dot-notation alphanumeric property' => [
                (object) ['1a' => true],
                '$.1a',
                ['true'],
                true,
            ],
            'Dot-notation null' => [
                (object) ['null' => true],
                '$.null',
                ['true'],
                true,
            ],
            'Dot-notation true' => [
                (object) ['true' => true],
                '$.true',
                ['true'],
                true,
            ],
            'Dot-notation false' => [
                (object) ['false' => true],
                '$.false',
                ['true'],
                true,
            ],
            'Dot-notation predicate' => [
                [1, 2, [3]],
                '$.[0]',
                ['1'],
                true,
            ],
            'Dot-notation star with predicate' => [
                [1, 2, [3]],
                '$.*[0]',
                ['3'],
                false,
            ],
            'Bracket-notation alpha property' => [
                (object) ['a' => true],
                '$["a"]',
                ['true'],
                true,
            ],
            'Bracket-notation numeric property' => [
                (object) ['1' => true],
                '$["1"]',
                ['true'],
                true,
            ],
            'Single index' => [
                ['a', 'b'],
                '$[1]',
                ['"b"'],
                true,
            ],
            'Nested dot-notation properties' => [
                (object) ['a' => (object) ['b' => false]],
                '$.a.b',
                ['false'],
                true,
            ],
            'All properties dot-notation' => [
                (object) ['a' => true, 'b' => false],
                '$.*',
                ['true', 'false'],
                false,
            ],
            'All indice dot-notation' => [
                ['a', 1],
                '$.*',
                ['"a"', '1'],
                false,
            ],
            'All properties bracket-notation' => [
                (object) ['a' => true, 'b' => false],
                '$[*]',
                ['true', 'false'],
                false,
            ],
            'All indice bracket-notation' => [
                ['a', 1],
                '$[*]',
                ['"a"', '1'],
                false,
            ],
            'Strict property list' => [
                (object) ['a' => true, 'b' => false, 'c' => 1],
                '$["a", "c"]',
                ['true', '1'],
                false,
            ],
            'Strict index list' => [
                [true, false, 1],
                '$[0, 2]',
                ['true', '1'],
                false,
            ],
            'Fully defined slice' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:6:2]',
                ['2', '4', '6'],
                false,
            ],
            'Slice defined without start' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[:6:2]',
                ['1', '3', '5'],
                false,
            ],
            'Slice defined without end' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[0::2]',
                ['1', '3', '5', '7'],
                false,
            ],
            'Slice defined without step value' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:3:]',
                ['2', '3'],
                false,
            ],
            'Slice defined without step' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:3]',
                ['2', '3'],
                false,
            ],
            'Slice defined without all values' => [
                [1, 2, 3],
                '$[::]',
                ['1', '2', '3'],
                false,
            ],
            'Slice defined without all values and step' => [
                [1, 2, 3],
                '$[:]',
                ['1', '2', '3'],
                false,
            ],
            'Slice defined with just negative start' => [
                [1, 2, 3],
                '$[-1:]',
                ['3'],
                false,
            ],
            'Slice defined with just negative index' => [
                [1, 2, 3],
                '$[-1]',
                ['3'],
                false,
            ],
            'Slice defined with just negative end' => [
                [1, 2, 3],
                '$[:-1]',
                ['1', '2'],
                false,
            ],
            'Slice defined with just negative step' => [
                [1, 2, 3],
                '$[::-1]',
                ['1', '2', '3'],
                false,
            ],
            'Slice defined with start and negative step' => [
                [1, 2, 3],
                '$[1::-1]',
                ['1', '2'],
                false,
            ],
            'Slice fully defined with and negative end and step' => [
                [1, 2, 3],
                '$[1:-4:-1]',
                ['1', '2'],
                false,
            ],
            'Simple filter with true' => [
                [1, 2, 3],
                '$[?(true)]',
                ['1', '2', '3'],
                false,
            ],
            'Simple filter with false' => [
                [1, 2, 3],
                '$[?(false)]',
                [],
                false,
            ],
            'Simple filter with true on all indice' => [
                [[1, 2], [3]],
                '$[*][?(true)]',
                ['1', '2', '3'],
                false,
            ],
            'Simple filter with existing path' => [
                (object) ['a' => (object) ['b' => 'c']],
                '$.a[?(@.b)]',
                ['{"b":"c"}'],
                false,
            ],
            'Simple filter with non-existing path' => [
                (object) ['a' => (object) ['c' => 'd']],
                '$.a[?(@.b)]',
                [],
                false,
            ],
            'Simple filter with partially existing path' => [
                [
                    (object) ['a' => (object) ['c' => 'd']],
                    (object) ['a' => (object) ['b' => 'c']],
                    (object) ['b' => (object) ['c' => 'd']],
                ],
                '$[?(@.a.b)]',
                ['{"a":{"b":"c"}}'],
                false,
            ],
            'Filter with EQ check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a == 1)]',
                ['{"a":1}'],
                false,
            ],
            'Filter with equality check on null' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => null],
                ],
                '$[?(@.a == null)]',
                ['{"a":null}'],
                false,
            ],
            'Filter with equality check on true' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => true],
                ],
                '$[?(@.a == true)]',
                ['{"a":true}'],
                false,
            ],
            'Filter with equality check on string' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 'b'],
                    (object) ['a' => 'c'],
                ],
                '$[?(@.a == "b")]',
                ['{"a":"b"}'],
                false,
            ],
            'Filter with equality check on false' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => false],
                ],
                '$[?(@.a == false)]',
                ['{"a":false}'],
                false,
            ],
            'Filter with equality check on boolean literals evaluating to true' => [
                [1, 2, 3],
                '$[?(true == true)]',
                ['1', '2', '3'],
                false,
            ],
            'Filter with equality check on boolean literals evaluating to false' => [
                [1, 2, 3],
                '$[?(true == false)]',
                [],
                false,
            ],
            'Filter with equality check on integer literals evaluating to true' => [
                [1, 2, 3],
                '$[?(1 == 1)]',
                ['1', '2', '3'],
                false,
            ],
            'Filter with equality check on integer literals evaluating to false' => [
                [1, 2, 3],
                '$[?(1 == 2)]',
                [],
                false,
            ],
            'Filter with equality check on string literals evaluating to true' => [
                [1, 2, 3],
                '$[?("a" == "a")]',
                ['1', '2', '3'],
                false,
            ],
            'Filter with equality check on string literals evaluating to false' => [
                [1, 2, 3],
                '$[?("a" == "b")]',
                [],
                false,
            ],
            'Filter with equality check on mixed type literals evaluating to false' => [
                [1, 2, 3],
                '$[?("a" == 1)]',
                [],
                false,
            ],
            'Filter with equality check on existing string paths evaluating to true' => [
                (object) ['a' => 'b', 'c' => 'b'],
                '$[?(@.a == @.c)]',
                ['{"a":"b","c":"b"}'],
                false,
            ],
            'Filter with equality check on existing string paths evaluating to false' => [
                (object) ['a' => 'b', 'c' => 'd'],
                '$[?(@.a == @.c)]',
                [],
                false,
            ],
            'Filter with equality check on existing array paths' => [
                [
                    (object) ['a' => ['b','d'], 'c' => ['b', 'd']],
                    (object) ['a' => ['b','d'], 'c' => ['b', 'd', 'e']],
                    (object) ['a' => ['b','d'], 'c' => ['d', 'b']],
                ],
                '$[?(@.a == @.c)]',
                ['{"a":["b","d"],"c":["b","d"]}'],
                false,
            ],
            'Filter with equality check on existing object paths' => [
                [
                    (object) ['a' => (object) ['b' => 1,'d' => 2], 'c' => (object) ['d' => 2, 'b' => 1]],
                    (object) ['a' => (object) ['b' => 2,'d' => 2], 'c' => (object) ['d' => 2, 'b' => 1]],
                    (object) ['a' => (object) ['b' => 1], 'c' => (object) ['d' => 2, 'b' => 1]],
                    (object) ['a' => (object) ['b' => 1,'d' => 2, 'e' => 3], 'c' => (object) ['d' => 2, 'b' => 1]],
                ],
                '$[?(@.a == @.c)]',
                ['{"a":{"b":1,"d":2},"c":{"d":2,"b":1}}'],
                false,
            ],
            'Filter with equality check of non-existing property with literal (issue #6)' => [
                (object) ['a' => (object) []],
                '$.a[?(@.b==1)]',
                [],
                false,
            ],
            'Filter with equality check of partial-existing property with literal (issue #6)' => [
                (object) [
                    'a' => [
                        (object) [
                            'b' => 1,
                        ],
                        (object) [
                            'b' => 2,
                            'c' => 3,
                        ],
                    ]
                ],
                '$.a[?(@.c==3)]',
                ['{"b":2,"c":3}'],
                false,
            ],
            'Filter with equality check of two partial-existing properties (issue #6)' => [
                (object) [
                    'a' => [
                        (object) [
                            'b' => 1,
                            'c' => 2,
                        ],
                        (object) [
                            'd' => 3,
                            'b' => 2,
                        ],
                        (object) [
                            'c' => 3,
                            'd' => 3,
                        ],
                    ]
                ],
                '$.a[?(@.c==@.d)]',
                ['{"c":3,"d":3}'],
                false,
            ],
            'Filter with equality check of two non-existing properties (issue #6)' => [
                (object) ['a' => (object) []],
                '$.a[?(@.b==@.c)]',
                [],
                false,
            ],
            'Filter with OR' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?(@.a || @.b)]',
                ['{"a":1,"b":2}', '{"a":3}', '{"b":5}'],
                false,
            ],
            'Filter with AND' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?(@.a && @.b)]',
                ['{"a":1,"b":2}'],
                false,
            ],
            'Filter with AND before OR without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?(@.a && @.b || @.c)]',
                ['{"a":1,"b":2}', '{"c":4}'],
                false,
            ],
            'Filter with AND after OR without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?(@.c || @.a && @.b)]',
                ['{"a":1,"b":2}', '{"c":4}'],
                false,
            ],
            'Filter with AND after OR with brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?((@.c || @.a) && @.b)]',
                ['{"a":1,"b":2}'],
                false,
            ],
            'Filter with OR after OR without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[?(@.c || @.a || @.b)]',
                ['{"a":1,"b":2}', '{"a":3}', '{"c":4}', '{"b":5}'],
                false,
            ],
            'Filter with AND after AND without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2, 'c' => 3],
                    (object) ['a' => 4],
                    (object) ['b' => 5],
                    (object) ['c' => 6],
                ],
                '$[?(@.c && @.a && @.b)]',
                ['{"a":1,"b":2,"c":3}'],
                false,
            ],
            'Filter with EQ after EQ without brackets' => [
                [
                    (object) ['a' => 1, 'b' => true, 'c' => 1],
                    (object) ['a' => 1, 'b' => false, 'c' => 2],
                    (object) ['a' => 3, 'b' => 3, 'c' => true],
                    (object) ['a' => 3, 'b' => 4, 'c' => false],
                ],
                '$[?(@.c == @.a == @.b)]',
                ['{"a":1,"b":true,"c":1}', '{"a":1,"b":false,"c":2}'],
                false,
            ],
            'Filter with EQ after EQ with brackets' => [
                [
                    (object) ['a' => 1, 'b' => true, 'c' => 1],
                    (object) ['a' => 1, 'b' => false, 'c' => 2],
                    (object) ['a' => 3, 'b' => 3, 'c' => true],
                    (object) ['a' => 3, 'b' => 4, 'c' => false],
                ],
                '$[?(@.c == (@.a == @.b))]',
                ['{"a":3,"b":3,"c":true}', '{"a":3,"b":4,"c":false}'],
                false,
            ],
            'Filter with true in brackets' => [
                [1, 2, 3],
                '$[?((true))]',
                ['1', '2', '3'],
                false,
            ],
            'Filter with false in brackets' => [
                [1, 2, 3],
                '$[?((false))]',
                [],
                false,
            ],
            'Filter with negated false' => [
                [1, 2, 3],
                '$[?(!false)]',
                ['1', '2', '3'],
                false,
            ],
            'Filter with EQ check on sring' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a == "а")]',
                ['{"a":"а"}'],
                false,
            ],
            'Filter with NEQ check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a != 1)]',
                ['{"a":2}'],
                false,
            ],
            'Filter with NEQ check on sring' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a != "а")]',
                ['{"a":"б"}'],
                false,
            ],
            'Filter with G check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a > 1)]',
                ['{"a":2}'],
                false,
            ],
            'Filter with G check on string' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a > "а")]',
                ['{"a":"б"}'],
                false,
            ],
            'Filter with LE check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a <= 1)]',
                ['{"a":1}'],
                false,
            ],
            'Filter with L check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a < 2)]',
                ['{"a":1}'],
                false,
            ],
            'Filter with GE check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a >= 1)]',
                ['{"a":1}','{"a":2}'],
                false,
            ],
            'Filter with path comparison to array' => [
                [
                    (object) ['a' => [1, 2]],
                    (object) ['a' => [1]],
                    (object) ['a' => [2]],
                ],
                '$[?(@.a == [1])]',
                ['{"a":[1]}'],
                false,
            ],
            'Filter with path comparison to array with path' => [
                [
                    (object) ['a' => [1], 'b' => 1],
                    (object) ['a' => [1, 2], 'b' => 2],
                    (object) ['a' => [2], 'b' => 1],
                ],
                '$[?(@.a == [1, @.b])]',
                ['{"a":[1,2],"b":2}'],
                false,
            ],
            'Deep scan of a property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['a' => 2]]],
                    (object) ['b' => (object) ['c' => 3]],
                ],
                '$..a',
                ['1', '{"b":{"a":2}}', '2'],
                false,
            ],
            'Deep scan of true property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['true' => 2]]],
                    (object) ['true' => (object) ['c' => 3]],
                ],
                '$..true',
                ['2', '{"c":3}'],
                false,
            ],
            'Deep scan of false property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['false' => 2]]],
                    (object) ['false' => (object) ['c' => 3]],
                ],
                '$..false',
                ['2', '{"c":3}'],
                false,
            ],
            'Deep scan of null property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['null' => 2]]],
                    (object) ['null' => (object) ['c' => 3]],
                ],
                '$..null',
                ['2', '{"c":3}'],
                false,
            ],
            'Deep scan of all children' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['a' => 2]]],
                    (object) ['b' => (object) ['c' => 3]],
                ],
                '$..*',
                [
                    '["a",{"a":1}]',
                    '"a"',
                    '{"a":1}',
                    '1',
                    '{"a":{"b":{"a":2}}}',
                    '{"b":{"a":2}}',
                    '{"a":2}',
                    '2',
                    '{"b":{"c":3}}',
                    '{"c":3}',
                    '3',
                ],
                false,
            ],
            'Deep scan of all children with comparative condition' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['a' => 2]]],
                    (object) ['b' => (object) ['c' => 3]],
                ],
                '$..*[?(@ < 3)]',
                ['1', '2'],
                false,
            ],
            'Deep scan of all children (double dot) with index 0 or 2' => [
                [1, 2, [3, 4, 5], [6, 7], 8],
                '$..[0, 2]',
                ['1', '[3,4,5]', '3', '5', '6'],
                false,
            ],
            'Deep scan of all children with (double dot with star) index 0 or 2' => [
                [1, 2, [3, 4, 5], [6, 7], 8],
                '$..*[0, 2]',
                ['3', '5', '6'],
                false,
            ],
            'Filter with regular expression without modifier' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /bc$/)]',
                ['{"a":"abc"}'],
                false,
            ],
            'Filter with regular expression with modifier' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /bc$/i)]',
                ['{"a":"abc"}', '{"a":"Bc"}'],
                false,
            ],
            'Filter with regular expression with escaped slash' => [
                [
                    (object) ['a' => 'ab/c'],
                    (object) ['a' => 'B/c'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\/c$/i)]',
                ['{"a":"ab/c"}', '{"a":"B/c"}'],
                false,
            ],
            'Filter with regular expression with escaped backslash' => [
                [
                    (object) ['a' => 'ab\\c'],
                    (object) ['a' => 'B\\c'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\\\\c$/i)]',
                ['{"a":"ab\\\\c"}', '{"a":"B\\\\c"}'],
                false,
            ],
            'Filter with regular expression with escaped non-slash' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\\c$/i)]',
                ['{"a":"abc"}', '{"a":"Bc"}'],
                false,
            ],
            'Aggregate function MIN' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => ['b', 2.1, 3]],
                    (object) ['a' => []],
                ],
                '$..a.min()',
                ['1', '2.1'],
                false,
            ],
            'Aggregate function MAX' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => [1, 2.1, 'b']],
                    (object) ['a' => []],
                ],
                '$..a.max()',
                ['3', '2.1'],
                false,
            ],
            'Aggregate function LENGTH' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => true],
                    (object) ['a' => (object) ['b' => 'c']],
                    (object) ['a' => []],
                    (object) ['a' => [(object) ['a' => [1, 2]]]],
                ],
                '$..a.length()',
                ['3', '0', '1', '2'],
                false,
            ],
            'Aggregate function of non-existing array' => [
                (object) ['a' => 1],
                '$.a.b.length()',
                [],
                true,
            ],
            'Aggregate function AVG' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => [1, 2, 'b']],
                    (object) ['a' => []],
                ],
                '$..a.avg()',
                ['2', '1.5'],
                false,
            ],
            'Aggregate function STDDEV' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => [1, 2, 1.5, 'b']],
                    (object) ['a' => [1]],
                    (object) ['a' => []],
                ],
                '$..a.stddev()',
                ['1', '0.5'],
                false,
            ],
        ];
    }
}
