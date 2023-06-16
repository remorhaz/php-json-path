<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class NodeValueListBuilder
{
    /**
     * @var list<int>
     */
    private array $outerIndexes = [];

    /**
     * @var list<NodeValueInterface>
     */
    private array $values = [];

    public function addValue(NodeValueInterface $value, int $outerIndex): self
    {
        if ($this->valueExists($value, $outerIndex)) {
            return $this;
        }

        $this->outerIndexes[] = $outerIndex;
        $this->values[] = $value;

        return $this;
    }

    public function build(): NodeValueListInterface
    {
        return new NodeValueList(new IndexMap(...$this->outerIndexes), ...$this->values);
    }

    private function valueExists(NodeValueInterface $value, int $outerIndex): bool
    {
        foreach ($this->values as $innerIndex => $addedValue) {
            if (!$addedValue->getPath()->equals($value->getPath())) {
                continue;
            }
            $addedOuterIndex = $this->outerIndexes[$innerIndex];

            return $outerIndex == $addedOuterIndex
                ? true
                : throw new Exception\ValueInListWithAnotherOuterIndexException($value, $addedOuterIndex, $outerIndex);
        }

        return false;
    }
}
