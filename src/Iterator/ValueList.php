<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_fill;
use function array_keys;
use function array_map;
use function in_array;

final class ValueList implements ValueListInterface
{

    private const TYPE_NODE = 0x01;

    private const TYPE_LITERAL = 0x02;

    private const TYPE_RESULT = 0x03;

    private $values;

    private $indexMap;

    private $type;

    public static function createRootNodes(NodeValueInterface ...$values): self
    {
        return self::createNodes(array_keys($values), ...$values);
    }

    public static function createNodes(array $indexMap, NodeValueInterface ...$values): self
    {
        return new self(self::TYPE_NODE, $indexMap, ...$values);
    }

    public static function createResults(array $indexMap, bool ...$results): self
    {
        return new self(
            self::TYPE_RESULT,
            $indexMap,
            ...array_map(
                function (bool $result): ResultValueInterface {
                    return new ResultValue($result);
                },
                $results
            )
        );
    }

    private function __construct(int $type, array $indexMap, ValueInterface ...$values)
    {
        $this->type = $type;
        $this->values = $values;
        $this->indexMap = $indexMap;
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
    public function getIndexMap(): array
    {
        return $this->indexMap;
    }

    public function getOuterIndex(int $valueIndex): int
    {
        if (!isset($this->indexMap[$valueIndex])) {
            throw new Exception\ValueOuterIndexNotFoundException($valueIndex);
        }

        return $this->indexMap[$valueIndex];
    }

    public function outerIndexExists(int $outerIndex): bool
    {
        return in_array($outerIndex, $this->indexMap, true);
    }

    public function pushIndexMap(): ValueListInterface
    {
        return new self($this->type, array_keys($this->values), ...$this->values);
    }

    public function popIndexMap(ValueListInterface $mapSource): ValueListInterface
    {
        $indexMap = [];
        foreach (array_keys($this->indexMap) as $index) {
            $indexMap[] = $mapSource->getOuterIndex($index);
        }

        return new self($this->type, $indexMap, ...$this->values);
    }

    public function withLiteral(LiteralValueInterface $value): ValueListInterface
    {
        return new self(
            self::TYPE_LITERAL,
            $this->indexMap,
            ...array_fill(0, \count($this->indexMap), $value)
        );
    }

    public function containsNodes(): bool
    {
        return self::TYPE_NODE == $this->type;
    }

    public function containsResults(): bool
    {
        return self::TYPE_RESULT == $this->type;
    }

    public function containsLiterals(): bool
    {
        return self::TYPE_LITERAL == $this->type;
    }
}
