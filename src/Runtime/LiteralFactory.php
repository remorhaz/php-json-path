<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\LiteralArrayValue;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Path\Value\LiteralValueList;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueList;
use Remorhaz\JSON\Path\Value\ValueListInterface;

class LiteralFactory implements LiteralFactoryInterface
{
    public function createScalar(NodeValueListInterface $source, $value): ValueListInterface
    {
        return new LiteralValueList($source->getIndexMap(), new LiteralScalarValue($value));
    }

    public function createArray(NodeValueListInterface $source, ValueListInterface ...$valueLists): ValueListInterface
    {
        return new ValueList(
            $source->getIndexMap(),
            ...array_map(
                fn (array $elements): ValueInterface => new LiteralArrayValue(...$elements),
                $this->buildArrayElementLists($source, ...$valueLists),
            )
        );
    }

    /**
     * @param NodeValueListInterface $source
     * @param ValueListInterface     ...$valueLists
     * @return array<int, list<ValueInterface>>
     */
    private function buildArrayElementLists(NodeValueListInterface $source, ValueListInterface ...$valueLists): array
    {
        $elementLists = array_fill_keys($source->getIndexMap()->getInnerIndexes(), []);
        foreach ($valueLists as $valueList) {
            if (!$source->getIndexMap()->equals($valueList->getIndexMap())) {
                throw new Exception\IndexMapMatchFailedException($valueList, $source);
            }
            foreach ($valueList->getValues() as $innerIndex => $value) {
                $elementLists[$innerIndex][] = $value;
            }
        }

        return $elementLists;
    }
}
