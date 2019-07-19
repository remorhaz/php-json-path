<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use function array_fill;
use function count;

final class LiteralArrayValueList implements ValueListInterface
{

    private $indexMap;

    private $values = [];

    public function __construct(IndexMapInterface $indexMap, ValueListInterface ...$valueLists)
    {
        $this->indexMap = $indexMap;
        $values = array_fill(0, count($this->indexMap), []);
        foreach ($valueLists as $listIndex => $valueList) {
            if (!$this->indexMap->equals($valueList->getIndexMap())) {
                throw new Exception\IndexMapMatchFailedException($valueList, $this);
            }

            foreach ($valueList->getValues() as $valueIndex => $value) {
                $values[$valueIndex][] = $value;
            }
        }
        foreach ($values as $index => $elements) {
            $this->values[$index] = new LiteralArrayValue($this->indexMap, ...$elements);
        }
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getValue(int $index): ValueInterface
    {
        if (!isset($this->values[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $this->values[$index];
    }
}
