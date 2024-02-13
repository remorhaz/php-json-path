<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Query\AstBuilder;
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
    private ?GrammarInterface $grammar = null;

    public function createParser(string $source, Tree $queryAst): Ll1Parser
    {
        try {
            $scheme = new TranslationScheme(new AstBuilder($queryAst));
            $parser = new Ll1Parser(
                $this->getGrammar(),
                $this->createSourceReader($source),
                new TranslationSchemeApplier($scheme),
            );
            $parser->loadLookupTable(__DIR__ . '/../../generated/LookupTable.php');

            return $parser;
        } catch (Throwable $e) {
            throw new Exception\ParserCreationFailedException($e);
        }
    }

    /**
     * @return GrammarInterface
     * @throws UnilexException
     */
    private function getGrammar(): GrammarInterface
    {
        return $this->grammar ??= GrammarLoader::loadFile(__DIR__ . '/../../spec/GrammarSpec.php');
    }

    /**
     * @param string $source
     * @return TokenReaderInterface
     * @throws UnilexException
     */
    private function createSourceReader(string $source): TokenReaderInterface
    {
        return new TokenReader(
            CharBufferFactory::createFromString($source),
            new TokenMatcher(),
            new TokenFactory($this->getGrammar()),
        );
    }
}
