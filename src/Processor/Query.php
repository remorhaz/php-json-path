<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function call_user_func;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;

final class Query implements QueryInterface
{

    private $valueIteratorFactory;

    private $runtime;

    private $callback;

    public function __construct(
        ValueIteratorFactoryInterface $valueIteratorFactory,
        RuntimeInterface $runtime,
        callable $callback
    ) {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->runtime = $runtime;
        $this->callback = $callback;
    }

    public function execute(NodeValueInterface $rootNode): ResultInterface
    {
        /** @var ValueListInterface $valueList */
        $valueList = call_user_func($this->callback, $this->runtime, $rootNode);

        return new Result($this->valueIteratorFactory, ...$valueList->getValues());
    }
}
