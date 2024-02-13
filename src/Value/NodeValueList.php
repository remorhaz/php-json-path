<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_values;

final class NodeValueList implements NodeValueListInterface
{
    /**
     * @var list<NodeValueInterface>
     */
    private array $values;

    public function __construct(
        private readonly IndexMapInterface $indexMap,
        NodeValueInterface ...$values,
    ) {
        $this->values = array_values($values);
    }

    public function getValue(int $index): ValueInterface
    {
        return $this->values[$index] ?? throw new Exception\ValueNotFoundException($index, $this);
    }

    /**
     * @return list<NodeValueInterface>
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
