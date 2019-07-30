<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\LiteralArrayValue;
use Remorhaz\JSON\Path\Value\ValueList;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Path\Value\LiteralValueList;
use Remorhaz\JSON\Path\Value\NodeValueList;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class Runtime implements RuntimeInterface
{

    private $valueListFetcher;

    private $valueFetcher;

    public function __construct(ValueListFetcher $valueListFetcher, ValueFetcherInterface $valueFetcher)
    {
        $this->valueListFetcher = $valueListFetcher;
        $this->valueFetcher = $valueFetcher;
    }

    public function fetchFilterContext(NodeValueListInterface $values): NodeValueListInterface
    {
        return $this
            ->valueListFetcher
            ->fetchFilterContext($values);
    }

    public function splitFilterContext(NodeValueListInterface $values): NodeValueListInterface
    {
        return new NodeValueList(
            $values->getIndexMap()->split(),
            ...$values->getValues()
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

    public function fetchFilteredValues(
        NodeValueListInterface $contextValues,
        EvaluatedValueListInterface $evaluatedValues
    ): NodeValueListInterface {
        return $this
            ->valueListFetcher
            ->fetchFilteredValues($evaluatedValues, $contextValues);
    }

    public function fetchChildren(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        return $this
            ->valueListFetcher
            ->fetchChildren($values, $matcher);
    }

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        return $this
            ->valueListFetcher
            ->fetchDeepChildren($values, $matcher);
    }

    public function matchAnyChild(): Matcher\ChildMatcherInterface
    {
        return new Matcher\AnyChildMatcher;
    }

    public function matchPropertyStrictly(string ...$nameList): Matcher\ChildMatcherInterface
    {
        return new Matcher\StrictPropertyMatcher(...$nameList);
    }

    public function matchElementStrictly(int ...$indexList): Matcher\ChildMatcherInterface
    {
        return new Matcher\StrictElementMatcher(...$indexList);
    }

    public function matchElementSlice(?int $start, ?int $end, ?int $step): Matcher\ChildMatcherInterface
    {
        return new Matcher\SliceElementMatcher($this->valueFetcher, $start, $end, $step);
    }

    public function createLiteralScalar(NodeValueListInterface $source, $value): ValueListInterface
    {
        return new LiteralValueList($source->getIndexMap(), new LiteralScalarValue($value));
    }

    public function createLiteralArray(
        NodeValueListInterface $source,
        ValueListInterface ...$valueLists
    ): ValueListInterface {
        $createArrayElement = function (array $elements) use ($source): ValueInterface {
            return new LiteralArrayValue($source->getIndexMap(), ...$elements);
        };

        return new ValueList(
            $source->getIndexMap(),
            ...array_map(
                $createArrayElement,
                $this->buildArrayElementLists($source, ...$valueLists)
            )
        );
    }

    private function buildArrayElementLists(NodeValueListInterface $source, ValueListInterface ...$valueLists): array
    {
        $elementLists = array_fill_keys($source->getIndexMap()->getInnerIndice(), []);
        foreach ($valueLists as $valueList) {
            if (!$source->getIndexMap()->equals($valueList->getIndexMap())) {
                throw new Exception\IndexMapMatchFailedException($valueList, $source);
            }
            foreach ($valueList->getValues() as $innerIndex => $value) {
                $elementLists[$innerIndex][] = $value;
            }
        }

        return $elementLists;
    }
}
