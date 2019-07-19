<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

interface QueryInterface
{

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface;
}
