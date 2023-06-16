<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_fill_keys;

final class LiteralValueList implements LiteralValueListInterface
{
    public function __construct(
        private IndexMapInterface $indexMap,
        private LiteralValueInterface $value,
    ) {
    }

    public function getLiteral(): LiteralValueInterface
    {
        return $this->value;
    }

    public function getValue(int $index): ValueInterface
    {
        $innerIndexes = $this->indexMap->getInnerIndexes();

        return isset($innerIndexes[$index])
            ? $this->value
            : throw new Exception\ValueNotFoundException($index, $this);
    }

    /**
     * @return list<LiteralValueInterface>
     */
    public function getValues(): array
    {
        return array_fill_keys($this->indexMap->getInnerIndexes(), $this->value);
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
