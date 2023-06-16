<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Processor\Exception;

final class QueryValidator implements QueryValidatorInterface
{
    public function getDefiniteQuery(QueryInterface $query): QueryInterface
    {
        return $query->getCapabilities()->isDefinite()
            ? $query
            : throw new Exception\IndefiniteQueryException($query);
    }

    public function getAddressableQuery(QueryInterface $query): QueryInterface
    {
        return $query->getCapabilities()->isAddressable()
            ? $query
            : throw new Exception\QueryNotAddressableException($query);
    }
}
