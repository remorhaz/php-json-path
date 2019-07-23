<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Query\QueryAstBuilder;
use Remorhaz\JSON\Path\TokenMatcher;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UnilexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Lexer\TokenReaderInterface;
use Remorhaz\UniLex\Parser\LL1\Parser as Ll1Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

final class Ll1ParserFactory implements Ll1ParserFactoryInterface
{

    private $grammar;

    public function createParser(string $path, Tree $queryAst): Ll1Parser
    {
        try {
            $scheme = new TranslationScheme(new QueryAstBuilder($queryAst));
            $parser = new Ll1Parser(
                $this->getGrammar(),
                $this->createPathReader($path),
                new TranslationSchemeApplier($scheme)
            );
            $parser->loadLookupTable(__DIR__ . '/../../generated/LookupTable.php');
        } catch (Throwable $e) {
            throw new Exception\ParserCreationFailedException($e);
        }

        return $parser;
    }

    /**
     * @return GrammarInterface
     * @throws UnilexException
     */
    private function getGrammar(): GrammarInterface
    {
        if (!isset($this->grammar)) {
            $this->grammar = GrammarLoader::loadFile(__DIR__ . '/../../spec/GrammarSpec.php');
        }

        return $this->grammar;
    }

    /**
     * @param string $path
     * @return TokenReaderInterface
     * @throws UnilexException
     */
    private function createPathReader(string $path): TokenReaderInterface
    {
        return new TokenReader(
            CharBufferFactory::createFromString($path),
            new TokenMatcher,
            new TokenFactory($this->getGrammar())
        );
    }
}
