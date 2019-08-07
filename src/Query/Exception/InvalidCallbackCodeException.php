<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Throwable;

final class InvalidCallbackCodeException extends LogicException implements ExceptionInterface
{

    private $callbackCode;

    public function __construct(string $callbackCode, Throwable $previous = null)
    {
        $this->callbackCode = $callbackCode;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid query callback code generated:\n\n{$this->callbackCode}";
    }

    public function getCallbackCode(): string
    {
        return $this->callbackCode;
    }
}
