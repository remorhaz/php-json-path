<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use LogicException;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Throwable;

final class QueryNotAddressableException extends LogicException implements ExceptionInterface
{

    private $query;

    public function __construct(QueryInterface $query, Throwable $previous = null)
    {
        $this->query = $query;
        parent::__construct("Query is not addressable", 0, $previous);
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
