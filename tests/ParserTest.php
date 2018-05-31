<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\TokenMatcher;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Parser\LL1\AbstractParserListener;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

class ParserTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @throws \Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException
     */
    public function testParser(): void
    {
        $buffer = CharBufferFactory::createFromString('$.a.b[?(@.x == 2)]');
        $grammar = GrammarLoader::loadFile(__DIR__ . '/../spec/GrammarSpec.php');
        $tokenFactory = new TokenFactory($grammar);
        $tokenMatcher = new TokenMatcher;
        $reader = new TokenReader($buffer, $tokenMatcher, $tokenFactory);
        $listener = new class extends AbstractParserListener {};
        $parser = new Parser($grammar, $reader, $listener);
        $parser->loadLookupTable(__DIR__ . '/../generated/LookupTable.php');
        $parser->run();
    }
}
