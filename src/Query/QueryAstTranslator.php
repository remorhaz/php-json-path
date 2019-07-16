<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;

final class QueryAstTranslator implements QueryAstTranslatorInterface
{

    private $queryCallbackBuilder;

    public function __construct(QueryCallbackBuilder $callbackBuilder)
    {
        $this->queryCallbackBuilder = $callbackBuilder;
    }

    public function buildQuery(Tree $queryAst): QueryInterface
    {
        $translator = new Translator($queryAst, $this->queryCallbackBuilder);
        $translator->run();

        $callback = $this
            ->queryCallbackBuilder
            ->getQueryCallback();

        return new Query($callback);
    }
}
