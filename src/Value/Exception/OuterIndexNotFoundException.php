<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use OutOfRangeException;
use Remorhaz\JSON\Path\Value\IndexMapInterface;
use Throwable;

final class OuterIndexNotFoundException extends OutOfRangeException implements ExceptionInterface
{
    public function __construct(
        private int $innerIndex,
        private IndexMapInterface $indexMap,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Outer index not found in index map for inner index $this->innerIndex";
    }

    public function getInnerIndex(): int
    {
        return $this->innerIndex;
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
