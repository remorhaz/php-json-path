<?php

namespace Remorhaz\JSON\Data;

interface NodeSetInterface
{

    /**
     * @return NodeInterface[]
     */
    public function getNodeList(): array;
}
