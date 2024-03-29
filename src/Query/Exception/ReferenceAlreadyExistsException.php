<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Throwable;

final class ReferenceAlreadyExistsException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly int $referenceId,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Reference #$this->referenceId already exists", previous: $previous);
    }

    public function getReferenceId(): int
    {
        return $this->referenceId;
    }
}
