<?php

namespace Remorhaz\JSON\Path\Runtime;

interface VariableInterface
{

    public const TYPE_STRING = 1;

    public const TYPE_INT = 2;

    public const TYPE_BOOL = 3;

    public const TYPE_NULL = 4;

    public const TYPE_NODE = 5;

    public const TYPE_NAN = 6;

    public const TYPE_MIXED = 7;

    public function getType(): int;

    public function getData();

    public function isList(): bool;

    /**
     * @return VariableInterface[]
     */
    public function getList(): array;

    public function append(VariableInterface $variable);

    public function isForked(): bool;

    public function getForkId(): int;

    public function setForkId(int $id);
}
