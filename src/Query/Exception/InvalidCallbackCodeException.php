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
        parent::__construct("Invalid query callback code generated", 0, $previous);
    }

    public function getCallbackCode(): string
    {
        return $this->callbackCode;
    }
}
