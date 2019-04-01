<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Data\Node;
use Remorhaz\JSON\Path\Data\NodeInterface;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIterator;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\Value;
use Remorhaz\JSON\Path\QueryBuilder;
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
        $iterator = EventIterator::create($json, $path);
        $values = [new Value($iterator, $path)];

        $fetcher = new Fetcher;
        $values = $fetcher->fetchChildren(
            new StrictPropertyMatcher('a', 'x'),
            ...$values
        );
        $values = $fetcher->fetchChildren(
            new StrictPropertyMatcher('b'),
            ...$values
        );

        $actualValue = [];
        foreach ($values as $value) {
            $actualValue[] = (new EventExporter($fetcher))->export($value->getIterator());
        }

        //self::assertEquals([(object) ['b' => 'c']], $actualValue);
        self::assertEquals(['c'], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     */
    public function testParser(): void
    {
        self::markTestSkipped('JSON Iterator not implemented');
        //$buffer = CharBufferFactory::createFromString('$.a.b[(@.x.length())]');
        $buffer = CharBufferFactory::createFromString('$.a.b[?(!(@.x.length() == 2))]');
        //$buffer = CharBufferFactory::createFromString('$.a.b["c\\"d\\\\e", \'f\']');
        $grammar = GrammarLoader::loadFile(__DIR__ . '/../spec/GrammarSpec.php');
        $tokenFactory = new TokenFactory($grammar);
        $tokenMatcher = new TokenMatcher;
        $reader = new TokenReader($buffer, $tokenMatcher, $tokenFactory);
        $queryBuilder = new QueryBuilder;
        $scheme = new TranslationScheme($queryBuilder);
        $listener = new TranslationSchemeApplier($scheme);
        $parser = new Parser($grammar, $reader, $listener);
        $parser->loadLookupTable(__DIR__ . '/../generated/LookupTable.php');
        $parser->run();
        $query = $queryBuilder->build();
        $query->execute($this->createDocumentRoot());
    }

    private function createDocumentRoot(): NodeInterface
    {
        $data = (object) ['a' => (object) ['b' => 1, 'c' => 2]];
        return new Node($data);
    }
}
