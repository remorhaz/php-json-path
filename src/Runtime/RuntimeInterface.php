<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

interface RuntimeInterface
{

    public function getInput(NodeValueInterface $rootValue): NodeValueListInterface;

    public function createFilterContext(NodeValueListInterface $values): NodeValueListInterface;

    public function splitFilterContext(NodeValueListInterface $values): NodeValueListInterface;

    public function joinFilterResults(
        EvaluatedValueListInterface $evaluatedValues,
        NodeValueListInterface $contextValues
    ): EvaluatedValueListInterface;

    public function fetchFilteredValues(
        NodeValueListInterface $contextValues,
        EvaluatedValueListInterface $evaluatedValues
    ): NodeValueListInterface;

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

    public function matchElementSlice(NodeValueListInterface $source, ?int $start, ?int $end, ?int $step): array;

    public function populateLiteral(NodeValueListInterface $source, LiteralValueInterface $value): ValueListInterface;

    public function populateArrayElements(
        NodeValueListInterface $source,
        ValueListInterface ...$values
    ): array;

    public function populateIndexList(NodeValueListInterface $source, int ...$indexList): array;

    public function populateNameList(NodeValueListInterface $source, string ...$nameList): array;

    public function createScalar($value): LiteralValueInterface;

    public function createArray(ValueListInterface $source, ArrayValueInterface ...$elements): ValueListInterface;
}
