<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use Collator;
use Remorhaz\JSON\Data\Export\Decoder;
use Remorhaz\JSON\Data\Export\Encoder;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Data\Path\PathInterface;
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

    private $pathEncoder;

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
            new ResultFactory($encoder, $decoder),
            new PathEncoder
        );
    }

    public function __construct(
        RuntimeInterface $runtime,
        ResultFactoryInterface $resultFactory,
        PathEncoderInterface $pathEncoder
    ) {
        $this->runtime = $runtime;
        $this->resultFactory = $resultFactory;
        $this->pathEncoder = $pathEncoder;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface
    {
        return $this
            ->resultFactory
            ->createResult($query($this->runtime, $rootNode));
    }

    public function selectPaths(QueryInterface $query, NodeValueInterface $rootNode): array
    {
        return array_map(
            [$this->pathEncoder, 'encodePath'],
            $this->selectValuePaths($query, $rootNode)
        );
    }

    /**
     * @param QueryInterface $query
     * @param NodeValueInterface $rootNode
     * @return PathInterface[]
     */
    private function selectValuePaths(QueryInterface $query, NodeValueInterface $rootNode): array
    {
        if (!$query->getProperties()->isPath()) {
            throw new Exception\PathNotSelectableException($query);
        }

        $results = [];
        foreach ($query($this->runtime, $rootNode)->getValues() as $value) {
            if (!$value instanceof NodeValueInterface) {
                throw new Exception\PathNotFoundInValueException($value);
            }

            $results[] = $value->getPath();
        }

        return $results;
    }
}
