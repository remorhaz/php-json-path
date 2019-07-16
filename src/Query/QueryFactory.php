<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Parser\ParserInterface;

final class QueryFactory implements QueryFactoryInterface
{

    private $parser;

    private $astTranslator;

    public function __construct(ParserInterface $parser, QueryAstTranslatorInterface $astTranslator)
    {
        $this->parser = $parser;
        $this->astTranslator = $astTranslator;
    }

    public function createQuery(string $path): QueryInterface
    {
        return new LazyQuery($path, $this->parser, $this->astTranslator);
    }
}
