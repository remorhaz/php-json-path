<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Iterator\Evaluator;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\TokenMatcher;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UnilexException;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Lexer\TokenReaderInterface;
use Remorhaz\UniLex\Parser\LL1\Parser;
use Remorhaz\UniLex\Parser\LL1\TranslationSchemeApplier;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Throwable;

final class TranslatorFactory implements TranslatorFactoryInterface
{

    private $fetcher;

    private $evaluator;

    private $grammar;

    public function __construct(Fetcher $fetcher, Evaluator $evaluator)
    {
        $this->fetcher = $fetcher;
        $this->evaluator = $evaluator;
    }

    public function createTranslationScheme(Tree $queryAst): TranslationSchemeInterface
    {
        return new TranslationScheme(new QueryAstBuilder($queryAst));
    }

    public function createParser(string $path, TranslationSchemeInterface $scheme): Parser
    {
        try {
            $parser = new Parser(
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
