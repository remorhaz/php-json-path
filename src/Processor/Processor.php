<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Collator;
use Remorhaz\JSON\Path\Iterator\Aggregator\ValueAggregatorCollection;
use Remorhaz\JSON\Path\Iterator\Comparator\ValueComparatorCollection;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\Evaluator;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Path\Parser\TranslatorFactory;
use Remorhaz\JSON\Path\Parser\TranslatorFactoryInterface;
use Throwable;

final class Processor implements ProcessorInterface
{

    private $valueIteratorFactory;

    private $translatorFactory;

    public static function create(): self
    {
        $valueIteratorFactory = new ValueIteratorFactory;
        $translatorFactory = new TranslatorFactory(
            new Fetcher($valueIteratorFactory),
            new Evaluator(
                new ValueComparatorCollection($valueIteratorFactory, new Collator('UTF-8')),
                new ValueAggregatorCollection($valueIteratorFactory)
            )
        );

        return new self($valueIteratorFactory, $translatorFactory);
    }

    public function __construct(
        ValueIteratorFactoryInterface $valueIteratorFactory,
        TranslatorFactoryInterface $translatorFactory
    ) {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->translatorFactory = $translatorFactory;
    }

    public function readDecoded(string $path, $decodedJson): ResultInterface
    {
        return new Result(
            $this->valueIteratorFactory,
            ...$this->readOutput($path, $this->createDecodedRootNode($decodedJson))
        );
    }

    private function readOutput(string $path, NodeValueInterface $rootNode): array
    {

        try {
            $scheme = $this
                ->translatorFactory
                ->createTranslationScheme($rootNode);
            $this
                ->translatorFactory
                ->createParser($path, $scheme)
                ->run();
        } catch (Throwable $e) {
            throw new Exception\TranslationFailedException($e);
        }

        return $scheme->getOutput();
    }

    private function createDecodedRootNode($decodedJson): NodeValueInterface
    {
        return (new NodeValueFactory)->createValue($decodedJson, Path::createEmpty());
    }
}
