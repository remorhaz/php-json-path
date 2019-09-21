<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueList;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;

final class ValueListFetcher implements ValueListFetcherInterface
{

    private $valueFetcher;

    public function __construct(ValueFetcherInterface $valueFetcher)
    {
        $this->valueFetcher = $valueFetcher;
    }

    /**
     * @param NodeValueListInterface $source
     * @param Matcher\ChildMatcherInterface $matcher
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this
                ->valueFetcher
                ->createChildrenIterator($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchChildrenDeep(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($source->getValues() as $sourceIndex => $sourceValue) {
            $children = $this
                ->valueFetcher
                ->createDeepChildrenIterator($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function merge(NodeValueListInterface ...$sources): NodeValueListInterface
    {
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($sources as $source) {
            foreach ($source->getValues() as $innerIndex => $value) {
                $nodesBuilder->addValue(
                    $value,
                    $source->getIndexMap()->getOuterIndex($innerIndex),
                );
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
                    ->createChildrenIterator(new Matcher\AnyChildMatcher, $sourceValue)
                : [$sourceValue];
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchFilteredValues(
        NodeValueListInterface $values,
        EvaluatedValueListInterface $results
    ): NodeValueListInterface {
        if (!$values->getIndexMap()->isCompatible($results->getIndexMap())) {
            throw new Exception\IndexMapMatchFailedException($values, $results);
        }
        $valueIndex = 0;
        $valueMap = [];
        foreach ($results->getIndexMap()->toArray() as $innerIndex => $outerIndex) {
            if (isset($outerIndex)) {
                $valueMap[$innerIndex] = $valueIndex++;
            }
        }
        $nodesBuilder = new NodeValueListBuilder;
        foreach ($values->getValues() as $index => $value) {
            if (!isset($valueMap[$index])) {
                continue;
            }
            $evaluatedValue = $results->getValue($valueMap[$index]);
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

    public function splitFilterContext(NodeValueListInterface $values): NodeValueListInterface
    {
        return new NodeValueList(
            $values->getIndexMap()->split(),
            ...$values->getValues(),
        );
    }

    public function joinFilterResults(
        EvaluatedValueListInterface $evaluatedValues,
        NodeValueListInterface $contextValues
    ): EvaluatedValueListInterface {
        return new EvaluatedValueList(
            $evaluatedValues->getIndexMap()->join($contextValues->getIndexMap()),
            ...$evaluatedValues->getResults()
        );
    }
}
