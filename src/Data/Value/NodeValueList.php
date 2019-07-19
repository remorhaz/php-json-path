<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use function array_keys;
use Remorhaz\JSON\Data\Exception;

final class NodeValueList implements NodeValueListInterface
{

    private $values;

    private $indexMap;

    /**
     * @param NodeValueInterface ...$values
     * @return NodeValueListInterface
     * @todo Probably single value is enough
     */
    public static function createRoot(NodeValueInterface ...$values): NodeValueListInterface
    {
        return new self(new IndexMap(...array_keys($values)), ...$values);
    }

    public function __construct(IndexMapInterface $indexMap, ValueInterface ...$values)
    {
        $this->values = $values;
        $this->indexMap = $indexMap;
    }

    public function getValue(int $index): ValueInterface
    {
        if (!isset($this->values[$index])) {
            throw new Exception\ValueNotFoundException($index);
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
