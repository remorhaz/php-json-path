<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;

interface ValueListFetcherInterface
{

    /**
     * @param NodeValueListInterface $source
     * @param Matcher\ChildMatcherInterface $matcher
     * @return NodeValueListInterface
     */
    public function fetchChildren(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function fetchChildrenDeep(
        NodeValueListInterface $source,
        Matcher\ChildMatcherInterface $matcher
    ): NodeValueListInterface;

    public function fetchFilterContext(NodeValueListInterface $source): NodeValueListInterface;

    public function fetchFilteredValues(
        NodeValueListInterface $values,
        EvaluatedValueListInterface $results
    ): NodeValueListInterface;

    public function splitFilterContext(NodeValueListInterface $values): NodeValueListInterface;

    public function joinFilterResults(
        EvaluatedValueListInterface $evaluatedValues,
        NodeValueListInterface $contextValues
    ): EvaluatedValueListInterface;
}
