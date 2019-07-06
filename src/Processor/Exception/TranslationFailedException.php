<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use RuntimeException;
use Throwable;

final class TranslationFailedException extends RuntimeException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("JSONPath translation failed", 0, $previous);
    }
}
