<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use LogicException;
use Throwable;

final class ReferenceAlreadyExistsException extends LogicException implements ExceptionInterface
{

    private $referenceId;

    public function __construct(int $referenceId, Throwable $previous = null)
    {
        $this->referenceId = $referenceId;
        parent::__construct("Reference #{$this->referenceId} already exists", 0, $previous);
    }

    public function getReferenceId(): int
    {
        return $this->referenceId;
    }
}
