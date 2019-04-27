<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_keys;

final class ValueList implements ValueListInterface
{

    private $values;

    private $outerMap;

    public static function create(ValueInterface ...$values): self
    {
        $outerMap = array_keys($values);
        return new self($outerMap, ...$values);
    }


    public function __construct(array $outerMap, ValueInterface ...$values)
    {
        $this->values = $values;
        $this->outerMap = $outerMap;
    }

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return int[]
     */
    public function getOuterMap(): array
    {
        return $this->outerMap;
    }

    public function getOuterIndex(int $valueIndex): int
    {
        if (!isset($this->outerMap[$valueIndex])) {
            throw new Exception\ValueOuterIndexNotFoundException($valueIndex);
        }

        return $this->outerMap[$valueIndex];
    }

    public function outerIndexExists(int $outerIndex): bool
    {
        return \in_array($outerIndex, $this->outerMap, true);
    }
}
