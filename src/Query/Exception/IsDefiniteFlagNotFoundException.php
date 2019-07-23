<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Throwable;

final class IsDefiniteFlagNotFoundException extends LogicException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("IsDefinite flag is accessed before being set", 0, $previous);
    }
}
