<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class AstTranslator implements AstTranslatorInterface
{

    private $queryCallbackBuilder;

    public function __construct(CallbackBuilderInterface $callbackBuilder)
    {
        $this->queryCallbackBuilder = $callbackBuilder;
    }

    public function buildQuery(string $source, Tree $queryAst): QueryInterface
    {
        try {
            $translator = new Translator($queryAst, $this->queryCallbackBuilder);
            $translator->run();
        } catch (Throwable $e) {
            throw new Exception\QueryAstNotTranslatedException($queryAst, $e);
        }

        return new Query($source, $this->queryCallbackBuilder);
    }
}
