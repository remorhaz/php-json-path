<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\Evaluator;
use Remorhaz\JSON\Path\Iterator\EventExporter;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\ValueComparatorCollection;
use Remorhaz\JSON\Path\Iterator\ValueIterator;
use Remorhaz\JSON\Path\TokenMatcher;
use Remorhaz\JSON\Path\TranslationScheme;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

class ParserTest extends TestCase
{

    public function testRuntime()
    {
        $json = (object) ['x'=> 1, 'a' => (object) ['b' => 'c']];
        // $[a, x].b
        $path = Path::createEmpty();
        $iteratorFactory = (new NodeValueFactory)->createValue($json, $path);
        $values = NodeValueList::createRoot($iteratorFactory);

        $valueIterator = new ValueIterator;
        $fetcher = new Fetcher($valueIterator);
        $values = $fetcher->fetchChildren(
            new StrictPropertyMatcher('a', 'x'),
            $values
        );
        $values = $fetcher->fetchChildren(
            new StrictPropertyMatcher('b'),
            $values
        );

        $actualValue = [];
        foreach ($values->getValues() as $value) {
            $actualValue[] = (new EventExporter($valueIterator))->export($value->createIterator());
        }

        //self::assertEquals([(object) ['b' => 'c']], $actualValue);
        self::assertEquals(['c'], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     * @dataProvider providerParser
     */
    public function testParser($json, string $query, array $expectedValue): void
    {
        //self::markTestSkipped('JSON Iterator not implemented');
        $buffer = CharBufferFactory::createFromString($query);
        $grammar = GrammarLoader::loadFile(__DIR__ . '/../spec/GrammarSpec.php');
        $tokenFactory = new TokenFactory($grammar);
        $tokenMatcher = new TokenMatcher;
        $reader = new TokenReader($buffer, $tokenMatcher, $tokenFactory);

        $path = Path::createEmpty();
        $rootValue = (new NodeValueFactory)->createValue($json, $path);
        $valueIterator = new ValueIterator;
        $fetcher = new Fetcher($valueIterator);
        $evaluator = new Evaluator(new ValueComparatorCollection($valueIterator));
        $scheme = new TranslationScheme($rootValue, $fetcher, $evaluator);
        $listener = new TranslationSchemeApplier($scheme);
        $parser = new Parser($grammar, $reader, $listener);
        $parser->loadLookupTable(__DIR__ . '/../generated/LookupTable.php');
        $parser->run();

        $output = $scheme->getOutput();
        $actualValue = [];
        foreach ($output as $value) {
            $actualValue[] = \json_encode((new EventExporter($valueIterator))->export($value->createIterator()));
        }

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
            'Filter with NEQ check on int' => [
                [
                    (object) ['a' => 1],
                    (object) ['a' => 2],
                ],
                '$[?(@.a != 1)]',
                ['{"a":2}'],
            ],
            'Filter with path comparison to array' => [
                [
                    (object) ['a' => [1, 2]],
                    (object) ['a' => [1]],
                    (object) ['a' => [2]],
                ],
                '$[?(@.a == [1,])]', // TODO: remove trailing comma
                ['{"a":[1]}'],
            ],
            'Filter with path comparison to array with path' => [
                [
                    (object) ['a' => [1], 'b' => 1],
                    (object) ['a' => [1, 2], 'b' => 2],
                    (object) ['a' => [2], 'b' => 1],
                ],
                '$[?(@.a == [1, @.b,])]', // TODO: remove trailing comma
                ['{"a":[1,2],"b":2}'],
            ],
        ];
    }
}
