<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use function array_fill_keys;
use Remorhaz\JSON\Data\Value\ValueInterface;

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
        $innerIndexes = $this->indexMap->getInnerIndice();
        if (!isset($innerIndexes[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $this->value;
    }

    public function getValues(): array
    {
        return array_fill_keys($this->indexMap->getInnerIndice(), $this->value);
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
