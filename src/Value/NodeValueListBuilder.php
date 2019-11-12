<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class NodeValueListBuilder
{

    private $outerIndexes = [];

    private $values = [];

    public function addValue(NodeValueInterface $value, int $outerIndex): self
    {
        $this->outerIndexes[] = $outerIndex;
        $this->values[] = $value;

        return $this;
    }

    public function build(): NodeValueListInterface
    {
        return new NodeValueList(new IndexMap(...$this->outerIndexes), ...$this->values);
    }
}
