<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;

interface ProcessorInterface
{

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface;
}
