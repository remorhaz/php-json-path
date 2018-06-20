<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Data\NodeInterface;
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

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     */
    public function testParser(): void
    {
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
        
    }
}
