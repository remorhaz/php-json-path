<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

final class Query implements QueryInterface
{

    private $runtime;

    private $callback;

    public function __construct(RuntimeInterface $runtime, callable $callback)
    {
        $this->runtime = $runtime;
        $this->callback = $callback;
    }
}
