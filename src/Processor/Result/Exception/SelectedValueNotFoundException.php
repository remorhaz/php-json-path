<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result\Exception;

use LogicException;
use Throwable;

final class SelectedValueNotFoundException extends LogicException implements ExceptionInterface
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Selected value not found", 0, $previous);
    }
}
