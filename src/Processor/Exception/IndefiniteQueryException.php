<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use Remorhaz\JSON\Path\Query\QueryInterface;
use RuntimeException;
use Throwable;

final class IndefiniteQueryException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly QueryInterface $query,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Query is indefinite", previous: $previous);
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
