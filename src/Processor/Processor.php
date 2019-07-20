<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Collator;
use Remorhaz\JSON\Data\Export\Decoder;
use Remorhaz\JSON\Data\Export\Encoder;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Runtime\Aggregator\AggregatorCollection;
use Remorhaz\JSON\Path\Runtime\Comparator\ComparatorCollection;
use Remorhaz\JSON\Path\Runtime\Evaluator;
use Remorhaz\JSON\Path\Runtime\Fetcher;
use Remorhaz\JSON\Path\Runtime\Runtime;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Processor implements ProcessorInterface
{

    private $runtime;

    private $resultFactory;

    public static function create(): ProcessorInterface
    {
        $valueIteratorFactory = new ValueIteratorFactory;
        $runtime = new Runtime(
            new Fetcher($valueIteratorFactory),
            new Evaluator(
                new ComparatorCollection($valueIteratorFactory, new Collator('UTF-8')),
                new AggregatorCollection($valueIteratorFactory)
            )
        );
        $decoder = new Decoder($valueIteratorFactory);
        $encoder = new Encoder($decoder);

        return new self(
            $runtime,
            new ResultFactory($encoder, $decoder)
        );
    }

    public function __construct(RuntimeInterface $runtime, ResultFactoryInterface $resultFactory)
    {
        $this->runtime = $runtime;
        $this->resultFactory = $resultFactory;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface
    {
        return $this
            ->resultFactory
            ->createResult($query($this->runtime, $rootNode));
    }
}
