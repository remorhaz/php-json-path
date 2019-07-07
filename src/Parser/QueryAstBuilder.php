<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UnilexException;

final class QueryAstBuilder implements QueryAstBuilderInterface
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
                ->createNode('getInput')
                ->getId();
        }

        return $this->inputId;
    }

    public function setOutput(int $id): void
    {
        $this
            ->tree
            ->setRootNode(
                $this
                    ->tree
                    ->createNode('output')
                    ->addChild($this->tree->getNode($id))
            );
    }

    public function createFilterContext(int $id): int
    {
        return $this
            ->tree
            ->createNode('createFilterContext')
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function split(int $id): int
    {
        return $this
            ->tree
            ->createNode('split')
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function evaluate(int $sourceId, int $id): int
    {
        return $this
            ->tree
            ->createNode('evaluate')
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function filter(int $contextId, int $evaluatedId): int
    {
        return $this
            ->tree
            ->createNode('filter')
            ->addChild($this->tree->getNode($contextId))
            ->addChild($this->tree->getNode($evaluatedId))
            ->getId();
    }

    public function calculateLogicalOr(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode('calculateLogicalOr')
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    public function calculateLogicalAnd(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode('calculateLogicalAnd')
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    public function calculateLogicalNot(int $id): int
    {
        return $this
            ->tree
            ->createNode('calculateLogicalNot')
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function calculateIsEqual(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode('calculateIsEqual')
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    public function calculateIsGreater(int $leftId, int $rightId): int
    {
        return $this
            ->tree
            ->createNode('calculateIsGreater')
            ->addChild($this->tree->getNode($leftId))
            ->addChild($this->tree->getNode($rightId))
            ->getId();
    }

    public function calculateIsRegExp(string $pattern, int $id): int
    {
        return $this
            ->tree
            ->createNode('calculateIsRegExp')
            ->addChild($this->tree->getNode($id))
            ->setAttribute('pattern', $pattern)
            ->getId();
    }

    public function fetchChildren(int $id, int $matcherId): int
    {
        return $this
            ->tree
            ->createNode('fetchChildren')
            ->addChild($this->tree->getNode($id))
            ->addChild($this->tree->getNode($matcherId))
            ->getId();
    }

    public function fetchChildrenDeep(int $id, int $matcherId): int
    {
        return $this
            ->tree
            ->createNode('fetchChildrenDeep')
            ->addChild($this->tree->getNode($id))
            ->addChild($this->tree->getNode($matcherId))
            ->getId();
    }

    public function matchAnyChild(): int
    {
        return $this
            ->tree
            ->createNode('matchAnyChild')
            ->getId();
    }

    public function matchPropertyStrictly(int $id): int
    {
        return $this
            ->tree
            ->createNode('matchPropertyStrictly')
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function matchElementStrictly(int $id): int
    {
        return $this
            ->tree
            ->createNode('matchElementStrictly')
            ->addChild($this->tree->getNode($id))
            ->getId();
    }

    public function calculateAggregate(string $name, int $id): int
    {
        return $this
            ->tree
            ->createNode('calculateAggregate')
            ->addChild($this->tree->getNode($id))
            ->setAttribute('name', $name)
            ->getId();
    }

    public function populateLiteralScalar(int $sourceId, $value): int
    {
        $valueNode = $this
            ->tree
            ->createNode('literalScalar')
            ->setAttribute('value', $value);

        return $this
            ->tree
            ->createNode('populateLiteralScalar')
            ->addChild($this->tree->getNode($sourceId))
            ->addChild($valueNode)
            ->getId();
    }

    public function populateLiteralArray(int $sourceId, int ...$valueIdList): int
    {
        $node = $this
            ->tree
            ->createNode('populateLiteralArray')
            ->addChild($this->tree->getNode($sourceId));

        foreach ($valueIdList as $valueId) {
            $node->addChild($this->tree->getNode($valueId));
        }

        return $node->getId();
    }

    public function populateIndexList(int $sourceId, int ...$indexList): int
    {
        return $this
            ->tree
            ->createNode('populateIndexList')
            ->setAttribute('indexList', $indexList)
            ->getId();
    }

    public function populateIndexSlice(int $sourceId, ?int $start, ?int $end, ?int $step): int
    {
        return $this
            ->tree
            ->createNode('populateIndexSlice')
            ->setAttribute('start', $start)
            ->setAttribute('end', $end)
            ->setAttribute('step', $step)
            ->getId();
    }

    public function populateNameList(int $sourceId, string ...$nameList): int
    {
        return $this
            ->tree
            ->createNode('populateNameList')
            ->addChild($this->tree->getNode($sourceId))
            ->setAttribute('nameList', $nameList)
            ->getId();
    }

    /**
     * @param string $name
     * @return Node
     * @deprecated
     */
    private function installNewNode(string $name): Node
    {
        return $this
            ->tree
            ->createNode($name);
    }
}
