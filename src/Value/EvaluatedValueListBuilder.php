<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

final class EvaluatedValueListBuilder
{

    private $indexMap = [];

    private $results = [];

    public function addResult(bool $result, int $outerIndex): self
    {
        $this->indexMap[] = $outerIndex;
        $this->results[] = $result;

        return $this;
    }

    public function build(): EvaluatedValueListInterface
    {
        return new EvaluatedValueList(new IndexMap(...$this->indexMap), ...$this->results);
    }
}
