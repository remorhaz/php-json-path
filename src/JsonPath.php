<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path;

use Collator;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\Comparator\ComparatorCollection;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Runtime\Evaluator;
use Remorhaz\JSON\Path\Runtime\Fetcher;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Path\Parser\Ll1ParserFactory;
use Remorhaz\JSON\Path\Parser\Parser;
use Remorhaz\JSON\Path\Query\QueryAstTranslator;
use Remorhaz\JSON\Path\Query\QueryCallbackBuilder;
use Remorhaz\JSON\Path\Processor\Processor;
use Remorhaz\JSON\Path\Processor\ProcessorInterface;
use Remorhaz\JSON\Path\Query\QueryFactory;
use Remorhaz\JSON\Path\Query\QueryFactoryInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Processor\ResultFactory;
use Remorhaz\JSON\Path\Processor\ResultFactoryInterface;
use Remorhaz\JSON\Path\Runtime\Runtime;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class JsonPath
{

    private $runtime;

    private $resultFactory;

    private $queryFactory;

    public static function create(): self
    {
        $valueIteratorFactory = new ValueIteratorFactory;
        $runtime = new Runtime(
            new Fetcher($valueIteratorFactory),
            new Evaluator(
                new ComparatorCollection($valueIteratorFactory, new Collator('UTF-8')),
                new AggregatorCollection($valueIteratorFactory)
            )
        );
        $queryFactory = new QueryFactory(
            new Parser(new Ll1ParserFactory),
            new QueryAstTranslator(new QueryCallbackBuilder)
        );

        return new self(
            $runtime,
            new ResultFactory($valueIteratorFactory),
            $queryFactory
        );
    }

    public function __construct(
        RuntimeInterface $runtime,
        ResultFactoryInterface $resultFactory,
        QueryFactoryInterface $queryFactory
    ) {
        $this->runtime = $runtime;
        $this->resultFactory = $resultFactory;
        $this->queryFactory = $queryFactory;
    }

    public function createProcessor(): ProcessorInterface
    {
        return new Processor($this->runtime, $this->resultFactory);
    }

    public function readDecodedJson($decodedJson): NodeValueInterface
    {
        return (new NodeValueFactory)->createValue($decodedJson, Path::createEmpty());
    }

    public function createQuery(string $path): QueryInterface
    {
        return $this
            ->queryFactory
            ->createQuery($path);
    }
}
