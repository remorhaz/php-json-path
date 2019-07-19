<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\LiteralValueInterface;
use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\NodeValueListInterface;
use Remorhaz\JSON\Data\ValueListInterface;

interface RuntimeInterface
{

    public function getInput(NodeValueInterface $rootValue): NodeValueListInterface;

    public function createFilterContext(NodeValueListInterface $values): NodeValueListInterface;

    public function split(NodeValueListInterface $values): NodeValueListInterface;

    public function evaluate(ValueListInterface $source, ValueListInterface $values): EvaluatedValueListInterface;

    public function filter(
        NodeValueListInterface $contextValues,
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
        Matcher\ChildMatcherInterface ...$matchers
    ): NodeValueListInterface;

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function matchAnyChild(NodeValueListInterface $source): array;

    public function matchPropertyStrictly(array $nameLists): array;

    public function matchElementStrictly(array $indexLists): array;

    public function aggregate(string $name, NodeValueListInterface $values): ValueListInterface;

    public function populateLiteral(NodeValueListInterface $source, LiteralValueInterface $value): ValueListInterface;

    public function populateLiteralArray(
        NodeValueListInterface $source,
        ValueListInterface ...$values
    ): ValueListInterface;

    public function populateIndexList(NodeValueListInterface $source, int ...$indexList): array;

    public function populateIndexSlice(NodeValueListInterface $source, ?int $start, ?int $end, ?int $step): array;

    public function populateNameList(NodeValueListInterface $source, string ...$nameList): array;

    public function createScalar($value): LiteralValueInterface;
}
