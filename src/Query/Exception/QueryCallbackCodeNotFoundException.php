<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Throwable;

final class QueryCallbackCodeNotFoundException extends LogicException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Query callback code is accessed before being generated", 0, $previous);
    }
}
