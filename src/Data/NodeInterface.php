<?php

namespace Remorhaz\JSON\Data;

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
     * @return int[]|string[]
     */
    public function getStructureKeys();

    public function hasParent(): bool;

    public function getParent(): NodeInterface;

    public function getProperty(): string;

    public function getIndex(): int;
}
