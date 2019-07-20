<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query\Exception;

use OutOfRangeException;
use Throwable;

final class ReferenceNotFoundException extends OutOfRangeException implements ExceptionInterface
{

    private $referenceId;

    public function __construct(int $referenceId, Throwable $previous = null)
    {
        $this->referenceId = $referenceId;
        parent::__construct("Reference #{$this->referenceId}", 0, $previous);
    }
}
