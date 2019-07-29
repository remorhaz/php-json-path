<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;

final class ValueListFetcher
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
                ->fetchValueChildren($matcher, $sourceValue);
            $outerIndex = $source->getIndexMap()->getOuterIndex($sourceIndex);
            foreach ($children as $child) {
                $nodesBuilder->addValue($child, $outerIndex);
            }
        }

        return $nodesBuilder->build();
    }

    public function fetchDeepChildren(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface $matcher
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
}
