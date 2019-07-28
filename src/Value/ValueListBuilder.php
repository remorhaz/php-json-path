<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ValueInterface;

final class ValueListBuilder
{

    private $indexMap = [];

    private $values = [];

    public function addValue(ValueInterface $value, int $outerIndex): self
    {
        $this->indexMap[] = $outerIndex;
        $this->values[] = $value;

        return $this;
    }

    public function build(): ValueListInterface
    {
        return new ValueList(new IndexMap(...$this->indexMap), ...$this->values);
    }
}
