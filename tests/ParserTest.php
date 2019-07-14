<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherList;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Processor\Result;

class ParserTest extends TestCase
{

    public function testRuntime()
    {
        $json = (object) ['x'=> 1, 'a' => (object) ['b' => 'c']];
        // $[a, x].b
        $path = Path::createEmpty();
        $iteratorFactory = (new NodeValueFactory)->createValue($json, $path);
        $values = NodeValueList::createRoot($iteratorFactory);

        $valueIteratorFactory = new ValueIteratorFactory;
        $fetcher = new Fetcher($valueIteratorFactory);
        $values = $fetcher->fetchChildren(
            $values,
            ...ChildMatcherList::populate(
                new StrictPropertyMatcher('a', 'x'),
                ...$values->getIndexMap()->getInnerIndice()
            )
        );
        $values = $fetcher->fetchChildren(
            $values,
            ...ChildMatcherList::populate(
                new StrictPropertyMatcher('b'),
                ...$values->getIndexMap()->getInnerIndice()
            )
        );

        $result = new Result($valueIteratorFactory, ...$values->getValues());
        self::assertEquals(['c'], $result->decode());
    }

    /**
     * @param $json
     * @param string $query
     * @param array $expectedValue
     * @dataProvider providerParser
     */
    public function testParser($json, string $query, array $expectedValue): void
    {
        $actualValue = Processor::create()
            ->readDecoded($query, $json)
            ->asJson();

        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerParser(): array
    {
        return [
            'Dot-notation property' => [
                (object) ['a' => true],
                '$.a',
                ['true'],
            ],
            'Dot-notation null' => [
                (object) ['null' => true],
                '$.null',
                ['true'],
            ],
            'Dot-notation true' => [
                (object) ['true' => true],
                '$.true',
                ['true'],
            ],
            'Dot-notation false' => [
                (object) ['false' => true],
                '$.false',
                ['true'],
            ],
            'Bracket-notation property' => [
                (object) ['a' => true],
                '$["a"]',
                ['true'],
            ],
            'Single index' => [
                ['a', 'b'],
                '$[1]',
                ['"b"'],
            ],
            'Nested dot-notation properties' => [
                (object) ['a' => (object) ['b' => false]],
                '$.a.b',
                ['false'],
            ],
            'All properties dot-notation' => [
                (object) ['a' => true, 'b' => false],
                '$.*',
                ['true', 'false'],
            ],
            'All indice dot-notation' => [
                ['a', 1],
                '$.*',
                ['"a"', '1'],
            ],
            'All properties bracket-notation' => [
                (object) ['a' => true, 'b' => false],
                '$[*]',
                ['true', 'false'],
            ],
            'All indice bracket-notation' => [
                ['a', 1],
                '$[*]',
                ['"a"', '1'],
            ],
            'Strict property list' => [
                (object) ['a' => true, 'b' => false, 'c' => 1],
                '$["a", "c"]',
                ['true', '1'],
            ],
            'Strict index list' => [
                [true, false, 1],
                '$[0, 2]',
                ['true', '1'],
            ],
            'Fully defined slice' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:6:2]',
                ['2', '4', '6'],
            ],
            'Slice defined without start' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[:6:2]',
                ['1', '3', '5'],
            ],
            'Slice defined without end' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[0::2]',
                ['1', '3', '5', '7'],
            ],
            'Slice defined without step value' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:3:]',
                ['2', '3'],
            ],
            'Slice defined without step' => [
                [1, 2, 3, 4, 5, 6, 7],
                '$[1:3]',
                ['2', '3'],
            ],
            'Slice defined without all values' => [
                [1, 2, 3],
                '$[::]',
                ['1', '2', '3'],
            ],
            'Slice defined without all values and step' => [
                [1, 2, 3],
                '$[:]',
                ['1', '2', '3'],
            ],
            'Slice defined with just negative start' => [
                [1, 2, 3],
                '$[-1:]',
                ['3'],
            ],
            'Slice defined with just negative end' => [
                [1, 2, 3],
                '$[:-1]',
                ['1', '2'],
            ],
            'Slice defined with just negative step' => [
                [1, 2, 3],
                '$[::-1]',
                ['1', '2', '3'],
            ],
            'Slice defined with start and negative step' => [
                [1, 2, 3],
                '$[1::-1]',
                ['1', '2'],
            ],
            'Slice fully defined with and negative end and step' => [
                [1, 2, 3],
                '$[1:-4:-1]',
                ['1', '2'],
            ],
            'Simple filter with true' => [
                [1, 2, 3],
                '$[?(true)]',
                ['1', '2', '3'],
            ],
            'Simple filter with false' => [
                [1, 2, 3],
                '$[?(false)]',
                [],
            ],
            'Simple filter with true on all indice' => [
                [[1, 2], [3]],
                '$[*][?(true)]',
                ['1', '2', '3'],
            ],
            'Simple filter with existing path' => [
                (object) ['a' => (object) ['b' => 'c']],
                '$.a[?(@.b)]',
                ['{"b":"c"}'],
            ],
            'Simple filter with non-existing path' => [
                (object) ['a' => (object) ['c' => 'd']],
                '$.a[?(@.b)]',
                [],
            ],
            'Simple filter with partially existing path' => [
                [
                    (object) ['a' => (object) ['c' => 'd']],
                    (object) ['a' => (object) ['b' => 'c']],
                    (object) ['b' => (object) ['c' => 'd']],
                ],
                '$[?(@.a.b)]',
                ['{"a":{"b":"c"}}'],
            ],
            'Filter with EQ check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a == 1)]',
                ['{"a":1}'],
            ],
            'Filter with equality check on null' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => null],
                ],
                '$[?(@.a == null)]',
                ['{"a":null}'],
            ],
            'Filter with equality check on true' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => true],
                ],
                '$[?(@.a == true)]',
                ['{"a":true}'],
            ],
            'Filter with equality check on string' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 'b'],
                    (object) ['a' => 'c'],
                ],
                '$[?(@.a == "b")]',
                ['{"a":"b"}'],
            ],
            'Filter with equality check on false' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => false],
                ],
                '$[?(@.a == false)]',
                ['{"a":false}'],
            ],
            'Filter with equality check on boolean literals evaluating to true' => [
                [1, 2, 3],
                '$[?(true == true)]',
                ['1', '2', '3'],
            ],
            'Filter with equality check on boolean literals evaluating to false' => [
                [1, 2, 3],
                '$[?(true == false)]',
                [],
            ],
            'Filter with equality check on integer literals evaluating to true' => [
                [1, 2, 3],
                '$[?(1 == 1)]',
                ['1', '2', '3'],
            ],
            'Filter with equality check on integer literals evaluating to false' => [
                [1, 2, 3],
                '$[?(1 == 2)]',
                [],
            ],
            'Filter with equality check on string literals evaluating to true' => [
                [1, 2, 3],
                '$[?("a" == "a")]',
                ['1', '2', '3'],
            ],
            'Filter with equality check on string literals evaluating to false' => [
                [1, 2, 3],
                '$[?("a" == "b")]',
                [],
            ],
            'Filter with equality check on mixed type literals evaluating to false' => [
                [1, 2, 3],
                '$[?("a" == 1)]',
                [],
            ],
            'Filter with equality check on existing string paths evaluating to true' => [
                (object) ['a' => 'b', 'c' => 'b'],
                '$[?(@.a == @.c)]',
                ['{"a":"b","c":"b"}'],
            ],
            'Filter with equality check on existing string paths evaluating to false' => [
                (object) ['a' => 'b', 'c' => 'd'],
                '$[?(@.a == @.c)]',
                [],
            ],
            'Filter with equality check on existing array paths' => [
                [
                    (object) ['a' => ['b','d'], 'c' => ['b', 'd']],
                    (object) ['a' => ['b','d'], 'c' => ['b', 'd', 'e']],
                    (object) ['a' => ['b','d'], 'c' => ['d', 'b']],
                ],
                '$[?(@.a == @.c)]',
                ['{"a":["b","d"],"c":["b","d"]}'],
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
            ],
            'Filter with true in brackets' => [
                [1, 2, 3],
                '$[?((true))]',
                ['1', '2', '3'],
            ],
            'Filter with false in brackets' => [
                [1, 2, 3],
                '$[?((false))]',
                [],
            ],
            'Filter with negated false' => [
                [1, 2, 3],
                '$[?(!false)]',
                ['1', '2', '3'],
            ],
            'Filter with EQ check on sring' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a == "а")]',
                ['{"a":"а"}'],
            ],
            'Filter with NEQ check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a != 1)]',
                ['{"a":2}'],
            ],
            'Filter with NEQ check on sring' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a != "а")]',
                ['{"a":"б"}'],
            ],
            'Filter with G check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a > 1)]',
                ['{"a":2}'],
            ],
            'Filter with G check on string' => [
                [
                    (object) ['a' => 'а'],
                    (object) ['a' => 'б'],
                ],
                '$[?(@.a > "а")]',
                ['{"a":"б"}'],
            ],
            'Filter with LE check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a <= 1)]',
                ['{"a":1}'],
            ],
            'Filter with L check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a < 2)]',
                ['{"a":1}'],
            ],
            'Filter with GE check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a >= 1)]',
                ['{"a":1}','{"a":2}'],
            ],
            'Filter with path comparison to array' => [
                [
                    (object) ['a' => [1, 2]],
                    (object) ['a' => [1]],
                    (object) ['a' => [2]],
                ],
                '$[?(@.a == [1])]',
                ['{"a":[1]}'],
            ],
            'Filter with path comparison to array with path' => [
                [
                    (object) ['a' => [1], 'b' => 1],
                    (object) ['a' => [1, 2], 'b' => 2],
                    (object) ['a' => [2], 'b' => 1],
                ],
                '$[?(@.a == [1, @.b])]',
                ['{"a":[1,2],"b":2}'],
            ],
            'Deep scan of a property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['a' => 2]]],
                    (object) ['b' => (object) ['c' => 3]],
                ],
                '$..a',
                ['1', '{"b":{"a":2}}', '2'],
            ],
            'Deep scan of true property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['true' => 2]]],
                    (object) ['true' => (object) ['c' => 3]],
                ],
                '$..true',
                ['2', '{"c":3}'],
            ],
            'Deep scan of false property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['false' => 2]]],
                    (object) ['false' => (object) ['c' => 3]],
                ],
                '$..false',
                ['2', '{"c":3}'],
            ],
            'Deep scan of null property' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['null' => 2]]],
                    (object) ['null' => (object) ['c' => 3]],
                ],
                '$..null',
                ['2', '{"c":3}'],
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
            ],
            'Deep scan of all children with comparative condition' => [
                [
                    ['a', (object) ['a' => 1]],
                    (object) ['a' => (object) ['b' => (object) ['a' => 2]]],
                    (object) ['b' => (object) ['c' => 3]],
                ],
                '$..*[?(@ < 3)]',
                ['1', '2'],
            ],
            'Filter with regular expression without modofier' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /bc$/)]',
                ['{"a":"abc"}'],
            ],
            'Filter with regular expression with modofier' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /bc$/i)]',
                ['{"a":"abc"}', '{"a":"Bc"}'],
            ],
            'Filter with regular expression with escaped slash' => [
                [
                    (object) ['a' => 'ab/c'],
                    (object) ['a' => 'B/c'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\/c$/i)]',
                ['{"a":"ab/c"}', '{"a":"B/c"}'],
            ],
            'Filter with regular expression with escaped backslash' => [
                [
                    (object) ['a' => 'ab\\c'],
                    (object) ['a' => 'B\\c'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\\\\c$/i)]',
                ['{"a":"ab\\\\c"}', '{"a":"B\\\\c"}'],
            ],
            'Filter with regular expression with escaped non-slash' => [
                [
                    (object) ['a' => 'abc'],
                    (object) ['a' => 'Bc'],
                    (object) ['a' => 'bca'],
                ],
                '$[?(@.a =~ /b\\c$/i)]',
                ['{"a":"abc"}', '{"a":"Bc"}'],
            ],
            'Aggregate function MIN' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => ['b', 2.1, 3]],
                    (object) ['a' => []],
                ],
                '$..a.min()',
                ['1', '2.1'],
            ],
            'Aggregate function MAX' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => [1, 2.1, 'b']],
                    (object) ['a' => []],
                ],
                '$..a.max()',
                ['3', '2.1'],
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
            ],
            'Aggregate function AVG' => [
                [
                    (object) ['a' => [1, 2, 3]],
                    (object) ['a' => [1, 2, 'b']],
                    (object) ['a' => []],
                ],
                '$..a.avg()',
                ['2', '1.5'],
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
            ],
        ];
    }
}
