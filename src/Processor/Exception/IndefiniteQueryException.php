<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use Remorhaz\JSON\Path\Query\QueryInterface;
use RuntimeException;
use Throwable;

final class IndefiniteQueryException extends RuntimeException implements ExceptionInterface
{

    private $query;

    public function __construct(QueryInterface $query, Throwable $previous = null)
    {
        $this->query = $query;
        parent::__construct("Query is indefinite", 0, $previous);
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
