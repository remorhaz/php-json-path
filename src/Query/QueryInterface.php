<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

interface QueryInterface
{

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface;
}
