<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Remorhaz\JSON\Path\Processor\Exception\ExceptionInterface;
use Throwable;

final class QueryExecutionFailedException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private string $source,
        private string $callbackCode,
        ?Throwable $previous = null,
    ) {
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Failed to execute JSONPath query: $this->source\n\n$this->callbackCode";
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getCallbackCode(): string
    {
        return $this->callbackCode;
    }
}
