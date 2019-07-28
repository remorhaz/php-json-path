<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use function array_fill_keys;
use function array_map;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LiteralArrayValueList implements ValueListInterface
{

    private $indexMap;

    private $values;

    private $valueLists;

    public function __construct(IndexMapInterface $indexMap, ValueListInterface ...$valueLists)
    {
        $this->indexMap = $indexMap;
        foreach ($valueLists as $valueList) {
            if (!$this->indexMap->equals($valueList->getIndexMap())) {
                throw new Exception\IndexMapMatchFailedException($valueList, $this);
            }
        }
        $this->valueLists = $valueLists;
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }

    public function getValues(): array
    {
        if (!isset($this->values)) {
            $this->values = $this->loadValues();
        }

        return $this->values;
    }

    private function loadValues(): array
    {
        $elementLists = array_fill_keys($this->indexMap->getInnerIndice(), []);
        foreach ($this->valueLists as $valueList) {
            foreach ($valueList->getValues() as $innerIndex => $value) {
                $elementLists[$innerIndex][] = $value;
            }
        }

        return array_map([$this, 'createValue'], $elementLists);
    }

    private function createValue(array $elements): ValueInterface
    {
        return new LiteralArrayValue($this->indexMap, ...$elements);
    }

    public function getValue(int $index): ValueInterface
    {
        $values = $this->getValues();
        if (!isset($values[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $values[$index];
    }
}
