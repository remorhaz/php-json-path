<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Processor implements ProcessorInterface
{

    private $runtime;

    private $resultFactory;

    public function __construct(RuntimeInterface $runtime, ResultFactoryInterface $resultFactory)
    {
        $this->runtime = $runtime;
        $this->resultFactory = $resultFactory;
    }

    public function select(QueryInterface $query, NodeValueInterface $rootNode): SelectResultInterface
    {
        return $this
            ->resultFactory
            ->createResult($query($this->runtime, $rootNode));
    }
}
