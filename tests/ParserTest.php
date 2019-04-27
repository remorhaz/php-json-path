<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\Value;
use Remorhaz\JSON\Path\Iterator\ValueList;
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
        $iteratorFactory = new EventIteratorFactory($json, $path);
        $values = ValueList::create(new Value($iteratorFactory));

        $fetcher = new Fetcher;
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
            $actualValue[] = (new EventExporter($fetcher))->export($value->createIterator());
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
        $rootValue = new EventIteratorFactory($json, $path);
        $fetcher = new Fetcher;
        $scheme = new TranslationScheme($rootValue, $fetcher);
        $listener = new TranslationSchemeApplier($scheme);
        $parser = new Parser($grammar, $reader, $listener);
        $parser->loadLookupTable(__DIR__ . '/../generated/LookupTable.php');
        $parser->run();

        $output = $scheme->getOutput();
        $actualValue = [];
        foreach ($output as $value) {
            $actualValue[] = \json_encode((new EventExporter($fetcher))->export($value->createIterator()));
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
            'Simple filter with integer that evaluates as true' => [
                [1, 2, 3],
                '$[*][?(1)]',
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
                    (object) ['a' => (object) ['b' => 'c']],
                    (object) ['a' => (object) ['c' => 'd']],
                    (object) ['b' => (object) ['c' => 'd']],
                ],
                '$[*][?(@.a.b)]',
                ['{"a":{"b":"c"}}'],
            ],
            'Filter with OR' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[*][?(@.a || @.b)]',
                ['{"a":1,"b":2}', '{"a":3}', '{"b":5}'],
            ],
            'Filter with AND' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[*][?(@.a && @.b)]',
                ['{"a":1,"b":2}'],
            ],
            'Filter with AND before OR without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[*][?(@.a && @.b || @.c)]',
                ['{"a":1,"b":2}', '{"c":4}'],
            ],
            'Filter with AND after OR without brackets' => [
                [
                    (object) ['a' => 1, 'b' => 2],
                    (object) ['a' => 3],
                    (object) ['c' => 4],
                    (object) ['b' => 5],
                ],
                '$[*][?(@.c || @.a && @.b)]',
                ['{"a":1,"b":2}', '{"c":4}'],
            ],
        ];
    }
}
