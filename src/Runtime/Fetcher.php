<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class Fetcher
{

    private $valueIteratorFactory;

    private $valueFetcher;

    public function __construct(ValueIteratorFactory $valueIteratorFactory, ValueFetcherInterface $valueFetcher)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->valueFetcher = $valueFetcher;
    }

    /**
     * @param NodeValueListInterface $source
     * @param Matcher\ChildMatcherInterface ...$matcherList
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface ...$matcherList
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $matcher = $matcherList[$sourceIndex];
            $children = $this
                ->valueFetcher
                ->fetchValueChildren($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchDeepChildren(
        Matcher\ChildMatcherInterface $matcher,
        NodeValueListInterface $source
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this
                ->valueFetcher
                ->fetchValueDeepChildren($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchFilterContext(NodeValueListInterface $source): NodeValueListInterface
    {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            if (!$sourceValue instanceof NodeValueInterface) {
                throw new Exception\InvalidContextValueException($sourceValue);
            }
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            $children = $sourceValue instanceof ArrayValueInterface
                ? $this
                    ->valueFetcher
                    ->fetchValueChildren(new Matcher\AnyChildMatcher, $sourceValue)
                : [$sourceValue];
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchFilteredValues(
        EvaluatedValueListInterface $results,
        NodeValueListInterface $values
    ): NodeValueListInterface {
        if (!$values->getIndexMap()->equals($results->getIndexMap())) {
            throw new Exception\IndexMapMatchFailedException($values, $results);
        }
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($values->getValues() as $index => $value) {
            $evaluatedValue = $results->getValue($index);
            if (!$evaluatedValue instanceof EvaluatedValueInterface) {
                throw new Exception\InvalidFilterValueException($evaluatedValue);
            }
            if (!$evaluatedValue->getData()) {
                continue;
            }
            $nodesBuilder->addValue(
                $value,
                $values->getIndexMap()->getOuterIndex($index)
            );
        }

        return $nodesBuilder->build();
    }

    /**
     * @param ValueListInterface $valueList
     * @return int[][]
     */
    public function fetchIndice(ValueListInterface $valueList): array
    {
        $result = [];
        foreach ($valueList->getValues() as $valueIndex => $value) {
            if (!$value instanceof ArrayValueInterface) {
                $result[$valueIndex] = [];
                continue;
            }

            $indice = [];
            $elementIterator = $this->valueIteratorFactory->createArrayIterator($value->createIterator());
            foreach ($elementIterator as $index => $element) {
                $indice[] = $index;
            }
            $result[$valueIndex] = $indice;
        }

        return $result;
    }
}
