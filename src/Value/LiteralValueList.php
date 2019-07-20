<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use function array_fill;
use function count;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LiteralValueList implements LiteralValueListInterface
{

    private $indexMap;

    private $value;

    private $values;

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
        $values = $this->getValues();
        if (!isset($values[$index])) {
            throw new Exception\ValueNotFoundException($index, $this);
        }

        return $values[$index];
    }

    public function getValues(): array
    {
        if (!isset($this->values)) {
            $this->values = array_fill(0, count($this->indexMap), $this->value);
        }

        return $this->values;
    }

    public function getIndexMap(): IndexMapInterface
    {
        return $this->indexMap;
    }
}
