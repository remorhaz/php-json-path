<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;

final class AstBuilder implements AstBuilderInterface
{
    private ?int $inputId = null;

    public function __construct(
        private Tree $tree,
    ) {
    }

    public function getInput(): int
    {
        return $this->inputId ??= $this
            ->tree
            ->createNode(AstNodeType::GET_INPUT)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function setOutput(int $id, bool $isDefinite, bool $isAddressable): void
    {
        $setOutputNode = $this
            ->tree
            ->createNode(AstNodeType::SET_OUTPUT)
            ->addChild($this->tree->getNode($id))
            ->setAttribute('is_definite', $isDefinite)
            ->setAttribute('is_addressable', $isAddressable);
        $this
            ->tree
            ->setRootNode($setOutputNode);
    }

    /**
     * @throws UniLexException
     */
    public function fetchFilterContext(int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::FETCH_FILTER_CONTEXT)
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function splitFilterContext(int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::SPLIT_FILTER_CONTEXT)
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function joinFilterResults(int $evaluatedId, int $contextId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::JOIN_FILTER_RESULTS)
            ->addChild($this->tree->getNode($evaluatedId))
            ->addChild($this->tree->getNode($contextId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function evaluate(int $sourceId, int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE)
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function filter(int $contextId, int $evaluatedId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::FILTER)
            ->addChild($this->tree->getNode($contextId))
            ->addChild($this->tree->getNode($evaluatedId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function evaluateLogicalOr(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_OR)
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function evaluateLogicalAnd(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_AND)
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function evaluateLogicalNot(int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_NOT)
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function calculateIsEqual(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CALCULATE_IS_EQUAL)
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function calculateIsGreater(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CALCULATE_IS_GREATER)
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function calculateIsRegExp(string $pattern, int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CALCULATE_IS_REGEXP)
            ->addChild($this->tree->getNode($id))
            ->setAttribute('pattern', $pattern)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function fetchChildren(int $id, int $matcherId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::FETCH_CHILDREN)
            ->addChild($this->tree->getNode($id))
            ->addChild($this->tree->getNode($matcherId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function fetchChildrenDeep(int $id, int $matcherId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::FETCH_CHILDREN_DEEP)
            ->addChild($this->tree->getNode($id))
            ->addChild($this->tree->getNode($matcherId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function merge(int ...$idList): int
    {
        $node = $this
            ->tree
            ->createNode(AstNodeType::MERGE);
        foreach ($idList as $id) {
            $node->addChild($this->tree->getNode($id));
        }

        return $node->getId();
    }

    public function matchAnyChild(): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ANY_CHILD)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function matchPropertyStrictly(string ...$names): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_PROPERTY_STRICTLY)
            ->setAttribute('names', $names)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function matchElementStrictly(int ...$indexes): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ELEMENT_STRICTLY)
            ->setAttribute('indexes', $indexes)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function matchElementSlice(?int $start, ?int $end, ?int $step): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ELEMENT_SLICE)
            ->setAttribute('hasStart', isset($start))
            ->setAttribute('start', $start)
            ->setAttribute('hasEnd', isset($end))
            ->setAttribute('end', $end)
            ->setAttribute('step', $step ?? 1)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function aggregate(string $name, int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::AGGREGATE)
            ->addChild($this->tree->getNode($id))
            ->setAttribute('name', $name)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function createScalar(int $sourceId, $value): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_LITERAL_SCALAR)
            ->setAttribute('value', $value)
            ->addChild($this->tree->getNode($sourceId))
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function createLiteralArray(int $sourceId, int $arrayId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_LITERAL_ARRAY)
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($arrayId))
            ->getId();
    }

    public function createArray(): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_ARRAY)
            ->getId();
    }

    /**
     * @throws UniLexException
     */
    public function appendToArray(int $arrayId, int $valueId): int
    {
        $appendNode = $this
            ->tree
            ->createNode(AstNodeType::APPEND_TO_ARRAY)
            ->addChild($this->tree->getNode($valueId));

        return $this
            ->tree
            ->getNode($arrayId)
            ->addChild($appendNode)
            ->getId();
    }
}
