<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;

interface ProcessorInterface
{

    public function select(QueryInterface $query, NodeValueInterface $rootNode): ResultInterface;
}
