<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectOnePathResultInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectOneResultInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectPathsResultInterface;
use Remorhaz\JSON\Path\Processor\Result\SelectResultInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;

interface ProcessorInterface
{

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface;

    public function selectOne(QueryInterface $query, NodeValueInterface $rootNode): SelectOneResultInterface;

    public function selectPaths(QueryInterface $query, NodeValueInterface $rootNode): SelectPathsResultInterface;

    public function selectOnePath(QueryInterface $query, NodeValueInterface $rootNode): SelectOnePathResultInterface;
}
