<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\Exception;

use OutOfRangeException;
use Remorhaz\JSON\Data\Value\IndexMapInterface;
use Throwable;

final class OuterIndexNotFoundException extends OutOfRangeException implements ExceptionInterface
{

    private $innerIndex;

    private $indexMap;

    public function __construct(int $innerIndex, IndexMapInterface $indexMap, Throwable $previous = null)
    {
        $this->innerIndex = $innerIndex;
        $this->indexMap = $indexMap;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Outer index not found in index map for inner index {$this->innerIndex}";
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
