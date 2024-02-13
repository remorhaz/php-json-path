<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use LogicException;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Throwable;

final class QueryNotAddressableException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly QueryInterface $query,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Query is not addressable", previous: $previous);
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
