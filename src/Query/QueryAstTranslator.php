<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class QueryAstTranslator implements QueryAstTranslatorInterface
{

    private $queryCallbackBuilder;

    public function __construct(QueryCallbackBuilderInterface $callbackBuilder)
    {
        $this->queryCallbackBuilder = $callbackBuilder;
    }

    public function buildQuery(string $source, Tree $queryAst): QueryInterface
    {
        try {
            $translator = new Translator($queryAst, $this->queryCallbackBuilder);
            $translator->run();
        } catch (Throwable $e) {
            throw new Exception\QueryAstNotTranslatedException($queryAst);
        }

        return new Query(
            $source,
            $this->queryCallbackBuilder->getQueryCallback(),
            $this->queryCallbackBuilder->getQueryProperties()
        );
    }
}
