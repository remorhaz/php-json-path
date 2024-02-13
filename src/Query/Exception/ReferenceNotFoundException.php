<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use OutOfRangeException;
use Throwable;

final class ReferenceNotFoundException extends OutOfRangeException implements ExceptionInterface
{
    public function __construct(
        private readonly int $referenceId,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Reference #$this->referenceId not found", previous: $previous);
    }

    public function getReferenceId(): int
    {
        return $this->referenceId;
    }
}
