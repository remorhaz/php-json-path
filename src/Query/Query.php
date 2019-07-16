<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use function call_user_func;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Query implements QueryInterface
{

    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface
    {
        return call_user_func($this->callback, $runtime, $rootNode);
    }
}
