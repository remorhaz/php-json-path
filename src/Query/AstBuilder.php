<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;

final class AstBuilder implements AstBuilderInterface
{

    private $inputId;

    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return int
     */
    public function getInput(): int
    {
        if (!isset($this->inputId)) {
            $this->inputId = $this
                ->tree
                ->createNode(AstNodeType::GET_INPUT)
                ->getId();
        }

        return $this->inputId;
    }

    /**
     * @param int $id
     * @param bool $isDefinite
     * @param bool $isPath
     * @throws UniLexException
     */
    public function setOutput(int $id, bool $isDefinite, bool $isPath): void
    {
        $this
            ->tree
            ->setRootNode(
                $this
                    ->tree
                    ->createNode(AstNodeType::SET_OUTPUT)
                    ->addChild($this->tree->getNode($id))
                    ->setAttribute('is_definite', $isDefinite)
                    ->setAttribute('is_path', $isPath)
            );
    }

    /**
     * @param int $id
     * @return int
     * @throws UniLexException
     */
    public function createFilterContext(int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_FILTER_CONTEXT)
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @param int $id
     * @return int
     * @throws UniLexException
     */
    public function split(int $id): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::SPLIT)
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param int $id
     * @return int
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
     * @param int $contextId
     * @param int $evaluatedId
     * @return int
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
     * @param int $leftEvaluatedId
     * @param int $rightEvaluatedId
     * @return int
     * @throws UniLexException
     */
    public function evaluateLogicalOr(int $leftEvaluatedId, int $rightEvaluatedId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_OR)
            ->addChild($this->tree->getNode($leftEvaluatedId))
            ->addChild($this->tree->getNode($rightEvaluatedId))
            ->getId();
    }

    /**
     * @param int $leftEvaluatedId
     * @param int $rightEvaluatedId
     * @return int
     * @throws UniLexException
     */
    public function evaluateLogicalAnd(int $leftEvaluatedId, int $rightEvaluatedId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_AND)
            ->addChild($this->tree->getNode($leftEvaluatedId))
            ->addChild($this->tree->getNode($rightEvaluatedId))
            ->getId();
    }

    /**
     * @param int $evaluatedId
     * @return int
     * @throws UniLexException
     */
    public function evaluateLogicalNot(int $evaluatedId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::EVALUATE_LOGICAL_NOT)
            ->addChild($this->tree->getNode($evaluatedId))
            ->getId();
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return int
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
     * @param int $leftId
     * @param int $rightId
     * @return int
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
     * @param string $pattern
     * @param int $id
     * @return int
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
     * @param int $id
     * @param int $matcherId
     * @return int
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
     * @param int $id
     * @param int $matcherId
     * @return int
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
     * @param int $sourceId
     * @return int
     * @throws UniLexException
     */
    public function matchAnyChild(int $sourceId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ANY_CHILD)
            ->addChild($this->tree->getNode($sourceId))
            ->getId();
    }

    /**
     * @param int $nameListId
     * @return int
     * @throws UniLexException
     */
    public function matchPropertyStrictly(int $nameListId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_PROPERTY_STRICTLY)
            ->addChild($this->tree->getNode($nameListId))
            ->getId();
    }

    /**
     * @param int $indexListId
     * @return int
     * @throws UniLexException
     */
    public function matchElementStrictly(int $indexListId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ELEMENT_STRICTLY)
            ->addChild($this->tree->getNode($indexListId))
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param int|null $start
     * @param int|null $end
     * @param int|null $step
     * @return int
     * @throws UniLexException
     */
    public function matchElementSlice(int $sourceId, ?int $start, ?int $end, ?int $step): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::MATCH_ELEMENT_SLICE)
            ->addChild($this->tree->getNode($sourceId))
            ->setAttribute('hasStart', isset($start))
            ->setAttribute('start', $start)
            ->setAttribute('hasEnd', isset($end))
            ->setAttribute('end', $end)
            ->setAttribute('step', $step ?? 1)
            ->getId();
    }

    /**
     * @param string $name
     * @param int $id
     * @return int
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
     * @param int $sourceId
     * @param int $valueId
     * @return int
     * @throws UniLexException
     */
    public function populateLiteral(int $sourceId, int $valueId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::POPULATE_LITERAL)
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($valueId))
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param int $arrayId
     * @return int
     * @throws UniLexException
     */
    public function populateArrayElements(int $sourceId, int $arrayId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::POPULATE_ARRAY_ELEMENTS)
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($arrayId))
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param int $elementsId
     * @return int
     * @throws UniLexException
     */
    public function createLiteralArray(int $sourceId, int $elementsId): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_LITERAL_ARRAY)
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($elementsId))
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param int ...$indexList
     * @return int
     * @throws UniLexException
     */
    public function populateIndexList(int $sourceId, int ...$indexList): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::POPULATE_INDEX_LIST)
            ->addChild($this->tree->getNode($sourceId))
            ->setAttribute('indexList', $indexList)
            ->getId();
    }

    /**
     * @param int $sourceId
     * @param string ...$nameList
     * @return int
     * @throws UniLexException
     */
    public function populateNameList(int $sourceId, string ...$nameList): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::POPULATE_NAME_LIST)
            ->addChild($this->tree->getNode($sourceId))
            ->setAttribute('nameList', $nameList)
            ->getId();
    }

    /**
     * @param $value
     * @return int
     * @throws UniLexException
     */
    public function createScalar($value): int
    {
        return $this
            ->tree
            ->createNode(AstNodeType::CREATE_SCALAR)
            ->setAttribute('value', $value)
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
     * @param int $arrayId
     * @param int $valueId
     * @return int
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