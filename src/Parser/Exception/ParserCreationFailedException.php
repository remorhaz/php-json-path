<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser\Exception;

use LogicException;
use Throwable;

final class ParserCreationFailedException extends LogicException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Failed to create JSONPath parser", 0, $previous);
    }
}
