<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Collator;
use Remorhaz\JSON\Data\Export\EventDecoder;
use Remorhaz\JSON\Data\Export\ValueDecoder;
use Remorhaz\JSON\Data\Export\ValueEncoder;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\ValueWalker;
use Remorhaz\JSON\Path\Processor\Mutator\Mutator;
use Remorhaz\JSON\Path\Processor\Mutator\MutatorInterface;
use Remorhaz\JSON\Path\Processor\Result\ValueResultInterface;
use Remorhaz\JSON\Path\Processor\Result\ResultFactory;
use Remorhaz\JSON\Path\Processor\Result\ResultFactoryInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectOnePathResultInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectPathsResultInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectResultInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Query\QueryValidator;
use Remorhaz\JSON\Path\Query\QueryValidatorInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\ComparatorCollection;
use Remorhaz\JSON\Path\Runtime\Evaluator;
use Remorhaz\JSON\Path\Runtime\LiteralFactory;
use Remorhaz\JSON\Path\Runtime\Matcher\MatcherFactory;
use Remorhaz\JSON\Path\Runtime\ValueListFetcher;
use Remorhaz\JSON\Path\Runtime\Runtime;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\JSON\Path\Runtime\ValueFetcher;

final class Processor implements ProcessorInterface
{

    private $runtime;

    private $resultFactory;

    private $queryValidator;

    private $mutator;

    public static function create(): ProcessorInterface
    {
        $runtime = new Runtime(
            new ValueListFetcher(new ValueFetcher()),
            new Evaluator(
                new ComparatorCollection(new Collator('UTF-8')),
                new AggregatorCollection(),
            ),
            new LiteralFactory(),
            new MatcherFactory(),
        );
        $jsonDecoder = new ValueDecoder();
        $jsonEncoder = new ValueEncoder($jsonDecoder);

        return new self(
            $runtime,
            new ResultFactory($jsonEncoder, $jsonDecoder, new PathEncoder()),
            new QueryValidator(),
            new Mutator(new ValueWalker(), new EventDecoder()),
        );
    }

    public function __construct(
        RuntimeInterface $runtime,
        ResultFactoryInterface $resultFactory,
        QueryValidatorInterface $queryValidator,
        MutatorInterface $mutator
    ) {
        $this->runtime = $runtime;
        $this->resultFactory = $resultFactory;
        $this->queryValidator = $queryValidator;
        $this->mutator = $mutator;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface
    {
        $values = $query($rootNode, $this->runtime);

        return $this
            ->resultFactory
            ->createSelectResult($values);
    }

    public function selectOne(QueryInterface $query, NodeValueInterface $rootNode): ValueResultInterface
    {
        $values = $this
            ->queryValidator
            ->getDefiniteQuery($query)($rootNode, $this->runtime);

        return $this
            ->resultFactory
            ->createSelectOneResult($values);
    }

    public function selectPaths(QueryInterface $query, NodeValueInterface $rootNode): SelectPathsResultInterface
    {
        $values = $this
            ->queryValidator
            ->getAddressableQuery($query)($rootNode, $this->runtime);

        return $this
            ->resultFactory
            ->createSelectPathsResult($values);
    }

    public function selectOnePath(QueryInterface $query, NodeValueInterface $rootNode): SelectOnePathResultInterface
    {
        $query = $this
            ->queryValidator
            ->getDefiniteQuery($query);
        $values = $this
            ->queryValidator
            ->getAddressableQuery($query)($rootNode, $this->runtime);

        return $this
            ->resultFactory
            ->createSelectOnePathResult($values);
    }

    public function delete(QueryInterface $query, NodeValueInterface $rootNode): ValueResultInterface
    {
        $paths = $this
            ->selectPaths($query, $rootNode)
            ->get();
        $value = $this
            ->mutator
            ->deletePaths($rootNode, ...$paths);

        return $this
            ->resultFactory
            ->createValueResult($value);
    }

    public function replace(
        QueryInterface $query,
        NodeValueInterface $rootNode,
        NodeValueInterface $newNode
    ): ValueResultInterface {
        $paths = $this
            ->selectPaths($query, $rootNode)
            ->get();
        $value = $this
            ->mutator
            ->replacePaths($rootNode, $newNode, ...$paths);

        return $this
            ->resultFactory
            ->createValueResult($value);
    }
}
