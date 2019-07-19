<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path;

use Collator;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Processor\SelectResultInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\Comparator\ComparatorCollection;
use Remorhaz\JSON\Path\Runtime\Evaluator;
use Remorhaz\JSON\Path\Runtime\Fetcher;
use Remorhaz\JSON\Data\Value\ValueIteratorFactory;
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
use Remorhaz\JSON\Path\Runtime\Runtime;

final class JsonPath implements QueryFactoryInterface, ProcessorInterface
{

    private $queryFactory;

    private $processor;

    public static function create(): self
    {
        return new self(
            self::createProcessor(),
            self::createQueryFactory()
        );
    }

    private static function createProcessor(): ProcessorInterface
    {
        $valueIteratorFactory = new ValueIteratorFactory;
        $runtime = new Runtime(
            new Fetcher($valueIteratorFactory),
            new Evaluator(
                new ComparatorCollection($valueIteratorFactory, new Collator('UTF-8')),
                new AggregatorCollection($valueIteratorFactory)
            )
        );
        return new Processor(
            $runtime,
            new ResultFactory($valueIteratorFactory)
        );
    }

    private static function createQueryFactory(): QueryFactoryInterface
    {
        return new QueryFactory(
            new Parser(new Ll1ParserFactory),
            new QueryAstTranslator(new QueryCallbackBuilder)
        );
    }

    public function __construct(
        ProcessorInterface $processor,
        QueryFactoryInterface $queryFactory
    ) {
        $this->processor = $processor;
        $this->queryFactory = $queryFactory;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface
    {
        return $this
            ->processor
            ->select($query, $rootNode);
    }

    public function createQuery(string $path): QueryInterface
    {
        return $this
            ->queryFactory
            ->createQuery($path);
    }
}
