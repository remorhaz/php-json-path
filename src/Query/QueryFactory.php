<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Parser\Ll1ParserFactory;
use Remorhaz\JSON\Path\Parser\Parser;
use Remorhaz\JSON\Path\Parser\ParserInterface;

final class QueryFactory implements QueryFactoryInterface
{
    public static function create(): QueryFactoryInterface
    {
        return new QueryFactory(
            new Parser(new Ll1ParserFactory()),
            new AstTranslator(),
        );
    }

    public function __construct(
        private readonly ParserInterface $parser,
        private readonly AstTranslatorInterface $astTranslator,
    ) {
    }

    public function createQuery(string $path): QueryInterface
    {
        return new LazyQuery($path, $this->parser, $this->astTranslator, new CallbackBuilder());
    }
}
