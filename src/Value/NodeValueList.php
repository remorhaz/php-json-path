<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class NodeValueList implements NodeValueListInterface
{

    private $values;

    private $indexMap;

    public function __construct(IndexMapInterface $indexMap, NodeValueInterface ...$values)
    {
        $this->values = $values;
        $this->indexMap = $indexMap;
    }

    public function getValue(int $index): ValueInterface
    {
        if (!isset($this->values[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $this->values[$index];
    }

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
