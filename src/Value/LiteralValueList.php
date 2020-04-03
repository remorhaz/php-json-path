<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_fill_keys;

final class LiteralValueList implements LiteralValueListInterface
{

    private $indexMap;

    private $value;

    public function __construct(IndexMapInterface $indexMap, LiteralValueInterface $value)
    {
        $this->indexMap = $indexMap;
        $this->value = $value;
    }

    public function getLiteral(): LiteralValueInterface
    {
        return $this->value;
    }

    public function getValue(int $index): ValueInterface
    {
        $innerIndexes = $this->indexMap->getInnerIndexes();
        if (!isset($innerIndexes[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $this->value;
    }

    public function getValues(): array
    {
        return array_fill_keys($this->indexMap->getInnerIndexes(), $this->value);
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
