<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Iterator\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Iterator\LiteralArrayValue;
use Remorhaz\JSON\Path\Iterator\LiteralValueInterface;
use Remorhaz\JSON\Path\Iterator\Matcher\ChildMatcherInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueListInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

interface RuntimeInterface
{

    public function getInput(): NodeValueListInterface;

    public function createFilterContext(NodeValueListInterface $values): NodeValueListInterface;

    public function split(NodeValueListInterface $values): NodeValueListInterface;

    public function evaluate(ValueListInterface $source, ValueListInterface $values): EvaluatedValueListInterface;

    public function filter(
        NodeValueListInterface $context,
        EvaluatedValueListInterface $evaluatedValues
    ): NodeValueListInterface;

    public function evaluateLogicalOr(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function evaluateLogicalAnd(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function evaluateLogicalNot(EvaluatedValueListInterface $values): EvaluatedValueListInterface;

    public function calculateIsEqual(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): ValueListInterface;

    public function calculateIsGreater(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): ValueListInterface;

    public function calculateIsRegExp(string $pattern, ValueListInterface $values): ValueListInterface;

    public function fetchChildren(
        NodeValueListInterface $values,
        ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function matchAnyChild(): ChildMatcherInterface;

    public function matchPropertyStrictly(array $nameLists): ChildMatcherInterface;

    public function matchElementStrictly(array $indexLists): ChildMatcherInterface;

    public function aggregate(string $name, NodeValueListInterface $values): ValueListInterface;

    public function populateLiteral(NodeValueListInterface $source, LiteralValueInterface $value): ValueListInterface;

    public function populateLiteralArray(NodeValueListInterface $source, LiteralValueInterface ...$values): ValueListInterface;

    public function populateIndexList(NodeValueListInterface $source, int ...$indexList): array;

    public function populateIndexSlice(NodeValueListInterface $source, ?int $start, ?int $end, ?int $step): array;

    public function populateNameList(NodeValueListInterface $source, string ...$nameList): array;

    public function createScalar($value): LiteralValueInterface;
}
