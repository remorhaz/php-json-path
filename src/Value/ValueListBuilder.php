<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ValueInterface;

final class ValueListBuilder
{

    private $outerIndexes = [];

    private $values = [];

    public function addValue(ValueInterface $value, int $outerIndex): self
    {
        $this->outerIndexes[] = $outerIndex;
        $this->values[] = $value;

        return $this;
    }

    public function build(): ValueListInterface
    {
        return new ValueList(new IndexMap(...$this->outerIndexes), ...$this->values);
    }
}
