<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

interface RuntimeInterface
{

    public function getInput(NodeValueInterface $rootValue): NodeValueListInterface;

    public function fetchFilterContext(NodeValueListInterface $values): NodeValueListInterface;

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
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function fetchChildrenDeep(
        NodeValueListInterface $values,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function matchAnyChild(): Matcher\ChildMatcherInterface;

    public function matchPropertyStrictly(string ...$nameList): Matcher\ChildMatcherInterface;

    public function matchElementStrictly(int ...$indexList): Matcher\ChildMatcherInterface;

    public function matchElementSlice(?int $start, ?int $end, ?int $step): Matcher\ChildMatcherInterface;

    public function createScalar(NodeValueListInterface $source, $value): ValueListInterface;

    public function populateArrayElements(
        NodeValueListInterface $source,
        ValueListInterface ...$values
    ): array;

    public function createArray(ValueListInterface $source, ArrayValueInterface ...$elements): ValueListInterface;
}
