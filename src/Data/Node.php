<?php

namespace Remorhaz\JSON\Path\Data;

use Remorhaz\JSON\Path\Exception;

class Node implements NodeInterface
{

    private $data;

    /**
     * @var NodeInterface|null
     */
    private $parent;

    /**
     * @var int|null
     */
    private $type;

    public function __construct($data, NodeParentInterface $parent = null)
    {
        $this->data = $data;
        $this->parent = $parent;
    }

    /**
     * @return NodeParentInterface
     * @throws Exception
     */
    public function getParent(): NodeParentInterface
    {
        if ($this->isRoot()) {
            throw new Exception("No parent in root node");
        }
        return $this->parent;
    }

    public function isRoot(): bool
    {
        return !isset($this->parent);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getType(): int
    {
        if (!isset($this->type)) {
            $this->type = $this->detectType();
        }
        return $this->type;
    }

    /**
     * @return bool|float|int|null|string
     * @throws Exception
     */
    public function getScalarValue()
    {
        switch ($this->getType()) {
            case self::TYPE_OBJECT:
                throw new Exception("Object is not a scalar type of data");

            case self::TYPE_ARRAY:
                throw new Exception("Array is not a scalar type of data");
        }
        return $this->data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPropertyList(): array
    {
        if ($this->getType() != self::TYPE_OBJECT) {
            throw new Exception("Only objects have properties");
        }
        return array_keys(get_object_vars($this->data));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getIndexList(): array
    {
        switch ($this->getType()) {
            case self::TYPE_OBJECT:
                return array_keys($this->getPropertyList());

            case self::TYPE_ARRAY:
                $nativeKeys = array_keys($this->data);
                $sequence = array_keys($nativeKeys);
                if ($nativeKeys != $sequence) {
                    throw new Exception("Invalid key sequence in array");
                }
                return $nativeKeys;
        }
        throw new Exception("Only structures have indices");
    }

    /**
     * @param int $index
     * @return NodeInterface
     * @throws Exception
     */
    public function getChildByIndex(int $index): NodeInterface
    {
        switch ($this->getType()) {
            case self::TYPE_OBJECT:
                $propertyList = $this->getPropertyList();
                if (!isset($propertyList[$index])) {
                    throw new Exception("Invalid property index: {$index}");
                }
                return $this->data->{$propertyList[$index]};

            case self::TYPE_ARRAY:
                $this->getIndexList(); // just checking for valid index sequence
                if (!isset($this->data[$index])) {
                    throw new Exception("Invalid array index: {$index}");
                }
                return $this->data[$index];

        }
        throw new Exception("Only structures have indices");
    }

    /**
     * @param string $property
     * @return NodeInterface
     * @throws Exception
     */
    public function getChildByProperty(string $property): NodeInterface
    {
        if ($this->getType() != self::TYPE_OBJECT) {
            throw new Exception("Only objects have properties");
        }
        if (!isset($this->data->{$property})) {
            throw new Exception("Invalid property: {$property}");
        }
        return $this->data->{$property};
    }

    /**
     * @return int
     * @throws Exception
     */
    private function detectType(): int
    {
        $nativeType = gettype($this->data);
        $typeMap = [
            'boolean' => self::TYPE_BOOL,
            'integer' => self::TYPE_NUMBER,
            'double' => self::TYPE_NUMBER,
            'string' => self::TYPE_STRING,
            'array' => self::TYPE_ARRAY,
            'object' => self::TYPE_OBJECT,
            'NULL' => self::TYPE_NULL,
        ];
        if (!isset($typeMap[$nativeType])) {
            throw new Exception("Invalid JSON data (type: {$nativeType})");
        }
        return $typeMap[$nativeType];
    }
}
