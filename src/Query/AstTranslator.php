<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Translator;
use Remorhaz\UniLex\AST\Tree;
use Throwable;

final class AstTranslator implements AstTranslatorInterface
{
    public function buildQuery(
        string $source,
        Tree $queryAst,
        CallbackBuilderInterface $callbackBuilder,
    ): QueryInterface {
        try {
            $translator = new Translator($queryAst, $callbackBuilder);
            $translator->run();
        } catch (Throwable $e) {
            throw new Exception\QueryAstNotTranslatedException($queryAst, $e);
        }

        return new Query($source, $callbackBuilder);
    }
}
