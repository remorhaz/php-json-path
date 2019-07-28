<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LiteralArrayValueList implements ValueListInterface
{

    private $indexMap;

    private $values;

    public function __construct(IndexMapInterface $indexMap, ArrayValueInterface ...$values)
    {
        $this->indexMap = $indexMap;
        $this->values = $values;
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
