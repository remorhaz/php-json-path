<?php

namespace Remorhaz\JSON\Path;

use Closure;
use Remorhaz\JSON\Path\Data\NodeInterface;
use Remorhaz\JSON\Path\Runtime\Runtime;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

class QueryBuilder
{

    private $codeLineList = [];

    public function addCodeLine(string ...$codeLineList)
    {
        foreach ($codeLineList as $codeLine) {
            $this->codeLineList[] = $codeLine;
        }
    }

    public function build(): QueryInterface
    {
        $executeHandler = $this->createExecuteHandler();

        return new class($executeHandler) implements QueryInterface
        {
            private $executeHandler;

            public function __construct(Closure $executeHandler)
            {
                $this->executeHandler = $executeHandler;
            }

            public function execute(NodeInterface $documentRoot)
            {
                call_user_func($this->executeHandler, $documentRoot);
            }
        };
    }

    private function createExecuteHandler(): Closure
    {
        return function (NodeInterface $documentRoot) {
            $runtime = $this->createRuntime($documentRoot);
            eval($this->buildCode());
        };
    }

    private function buildCode(): string
    {
        return implode(PHP_EOL, $this->codeLineList);
    }

    private function createRuntime(NodeInterface $documentRoot): RuntimeInterface
    {
        return new Runtime($documentRoot);
    }
}
