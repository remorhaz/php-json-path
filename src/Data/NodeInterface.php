<?php

namespace Remorhaz\JSON\Path\Data;

interface NodeInterface
{

    public const TYPE_NUMBER    = 0x01;
    public const TYPE_STRING    = 0x02;
    public const TYPE_BOOL      = 0x03;
    public const TYPE_NULL      = 0x04;
    public const TYPE_OBJECT    = 0x05;
    public const TYPE_ARRAY     = 0x06;

    public function getType(): int;

    /**
     * @return int|float|string|null|bool
     */
    public function getScalarValue();

    /**
     * @return string[]
     */
    public function getPropertyList(): array;

    public function getIndexList(): array;

    public function getChildByProperty(string $property): NodeInterface;

    public function getChildByIndex(int $index): NodeInterface;

    public function isRoot(): bool;

    public function getParent(): NodeInterface;

    public function getOwnProperty(): string;

    public function getOwnIndex(): int;
}
