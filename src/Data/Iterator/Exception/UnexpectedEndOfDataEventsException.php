<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator\Exception;

use RuntimeException;
use Throwable;

final class UnexpectedEndOfDataEventsException extends RuntimeException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Unexpected end of data events", 0, $previous);
    }
}
