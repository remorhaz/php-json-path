<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueList;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\IndexMap;
use Remorhaz\JSON\Path\Value\LiteralArrayValue;
use Remorhaz\JSON\Path\Value\LiteralArrayValueList;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;
use Remorhaz\JSON\Path\Value\LiteralValueList;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueList;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class Runtime implements RuntimeInterface
{

    private $fetcher;

    private $evaluator;

    public function __construct(Fetcher $fetcher, Evaluator $evaluator)
    {
        $this->fetcher = $fetcher;
        $this->evaluator = $evaluator;
    }

    public function getInput(NodeValueInterface $rootValue): NodeValueListInterface
    {
        return new NodeValueList(new IndexMap(0), $rootValue);
    }

    public function createFilterContext(NodeValueListInterface $values): NodeValueListInterface
    {
        return $this
            ->fetcher
            ->fetchFilterContext($values);
    }

    public function split(NodeValueListInterface $values): NodeValueListInterface
    {
        return new NodeValueList(
            $values->getIndexMap()->split(),
            ...$values->getValues()
        );
    }

    public function evaluate(ValueListInterface $source, ValueListInterface $values): EvaluatedValueListInterface
    {
        return $this
            ->evaluator
            ->evaluate($source, $values);
    }

    public function filter(
        NodeValueListInterface $contextValues,
        EvaluatedValueListInterface $evaluatedValues
    ): NodeValueListInterface {
        return $this
            ->fetcher
            ->filterValues(
                new ValueListFilter(
                    new EvaluatedValueList(
                        $evaluatedValues->getIndexMap()->join($contextValues->getIndexMap()),
                        ...$evaluatedValues->getResults()
                    )
                ),
                $contextValues
            );
    }

    public function evaluateLogicalOr(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface {
        return $this
            ->evaluator
            ->logicalOr($leftValues, $rightValues);
    }

    public function evaluateLogicalAnd(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface {
        return $this
            ->evaluator
            ->logicalAnd($leftValues, $rightValues);
    }

    public function evaluateLogicalNot(EvaluatedValueListInterface $values): EvaluatedValueListInterface
    {
        return $this
            ->evaluator
            ->logicalNot($values);
    }

    public function calculateIsEqual(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): ValueListInterface {
        return $this
            ->evaluator
            ->isEqual($leftValues, $rightValues);
    }

    public function calculateIsGreater(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): ValueListInterface {
        return $this
            ->evaluator
            ->isGreater($leftValues, $rightValues);
    }

    public function calculateIsRegExp(string $pattern, ValueListInterface $values): ValueListInterface
    {
        return $this
            ->evaluator
            ->isRegExp($values, $pattern); // TODO: Make arguments order consistent
    }

    public function fetchChildren(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface ...$matchers
    ): NodeValueListInterface {
        return $this
            ->fetcher
            ->fetchChildren($values, ...$matchers);
    }

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        return $this
            ->fetcher
            ->fetchDeepChildren($matcher, $values);
    }

    public function matchAnyChild(NodeValueListInterface $source): array
    {
        return array_map(
            function (): Matcher\ChildMatcherInterface {
                return new Matcher\AnyChildMatcher;
            },
            $source->getIndexMap()->getInnerIndice()
        );
    }

    public function matchPropertyStrictly(array $nameLists): array
    {
        return array_map(
            function (array $nameList): Matcher\ChildMatcherInterface {
                return new Matcher\StrictPropertyMatcher(...$nameList);
            },
            $nameLists
        );
    }

    public function matchElementStrictly(array $indexLists): array
    {
        return array_map(
            function (array $indexList): Matcher\ChildMatcherInterface {
                return new Matcher\StrictElementMatcher(...$indexList);
            },
            $indexLists
        );
    }

    public function aggregate(string $name, NodeValueListInterface $values): ValueListInterface
    {
        return $this
            ->evaluator
            ->aggregate($name, $values);
    }

    public function populateLiteral(NodeValueListInterface $source, LiteralValueInterface $value): ValueListInterface
    {
        return new LiteralValueList($source->getIndexMap(), $value);
    }

    public function populateArrayElements(
        NodeValueListInterface $source,
        ValueListInterface ...$values
    ): array {
        foreach ($values as $valueList) {
            if (!$source->getIndexMap()->equals($valueList->getIndexMap())) {
                throw new Exception\IndexMapMatchFailedException($valueList, $source);
            }
        }
        $elementLists = array_fill_keys($source->getIndexMap()->getInnerIndice(), []);
        foreach ($values as $valueList) {
            foreach ($valueList->getValues() as $innerIndex => $value) {
                $elementLists[$innerIndex][] = $value;
            }
        }

        $createArrayElement = function (array $elements) use ($source): ValueInterface {
            return new LiteralArrayValue($source->getIndexMap(), ...$elements);
        };

        return array_map($createArrayElement, $elementLists);
    }

    public function populateIndexList(NodeValueListInterface $source, int ...$indexList): array
    {
        return array_fill_keys(
            $source
                ->getIndexMap()
                ->getInnerIndice(),
            $indexList
        );
    }

    public function populateIndexSlice(NodeValueListInterface $source, ?int $start, ?int $end, ?int $step): array
    {
        return $this
            ->fetcher
            ->fetchSliceIndice($source, $start, $end, $step);
    }

    public function populateNameList(NodeValueListInterface $source, string ...$nameList): array
    {
        return array_fill_keys(
            $source
                ->getIndexMap()
                ->getInnerIndice(),
            $nameList
        );
    }

    public function createScalar($value): LiteralValueInterface
    {
        return new LiteralScalarValue($value);
    }

    public function createArray(ValueListInterface $source, ArrayValueInterface ...$elements): ValueListInterface
    {
        return new LiteralArrayValueList($source->getIndexMap(), ...$elements);
    }
}
