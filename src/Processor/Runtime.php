<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Iterator\EvaluatedValueList;
use Remorhaz\JSON\Path\Iterator\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Iterator\Evaluator;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\LiteralArrayValueList;
use Remorhaz\JSON\Path\Iterator\LiteralScalarValue;
use Remorhaz\JSON\Path\Iterator\LiteralValueInterface;
use Remorhaz\JSON\Path\Iterator\LiteralValueList;
use Remorhaz\JSON\Path\Iterator\Matcher\AnyChildMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictElementMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\StrictPropertyMatcher;
use Remorhaz\JSON\Path\Iterator\Matcher\ValueListFilter;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueList;
use Remorhaz\JSON\Path\Iterator\NodeValueListInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

final class Runtime implements RuntimeInterface
{

    private $fetcher;

    private $evaluator;

    private $input;

    public function __construct(Fetcher $fetcher, Evaluator $evaluator, NodeValueInterface $rootValue)
    {
        $this->fetcher = $fetcher;
        $this->evaluator = $evaluator;
        $this->input = NodeValueList::createRoot($rootValue);
    }

    public function getInput(): NodeValueListInterface
    {
        return $this->input;
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
        ChildMatcherInterface ...$matchers
    ): NodeValueListInterface {
        //$matchers = ChildMatcherList::populate($matcher, ...$values->getIndexMap()->getInnerIndice());

        return $this
            ->fetcher
            ->fetchChildren($values, ...$matchers);
    }

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        ChildMatcherInterface $matcher
    ): NodeValueListInterface {
        return $this
            ->fetcher
            ->fetchDeepChildren($matcher, $values);
    }

    public function matchAnyChild(NodeValueListInterface $source): array
    {
        return array_map(
            function (): ChildMatcherInterface {
                return new AnyChildMatcher;
            },
            $source->getIndexMap()->getInnerIndice()
        );
    }

    public function matchPropertyStrictly(array $nameLists): array
    {
        return array_map(
            function (array $nameList): ChildMatcherInterface {
                return new StrictPropertyMatcher(...$nameList);
            },
            $nameLists
        );
    }

    public function matchElementStrictly(array $indexLists): array
    {
        return array_map(
            function (array $indexList): ChildMatcherInterface {
                return new StrictElementMatcher(...$indexList);
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

    public function populateLiteralArray(
        NodeValueListInterface $source,
        ValueListInterface ...$values
    ): ValueListInterface {
        return new LiteralArrayValueList($source->getIndexMap(), ...$values);
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
}
