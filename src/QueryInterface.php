<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\JSON\Path\Data\NodeInterface;

interface QueryInterface
{

    public function execute(NodeInterface $documentRoot);
}
