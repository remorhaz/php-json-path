<?php

namespace Remorhaz\JSON\Path\Data;

interface NodeParentInterface
{

    public function getNode(): NodeInterface;

    public function getProperty(): string;

    public function getIndex(): int;
}
