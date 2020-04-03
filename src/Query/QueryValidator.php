<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Processor\Exception;

final class QueryValidator implements QueryValidatorInterface
{

    public function getDefiniteQuery(QueryInterface $query): QueryInterface
    {
        if (!$query->getCapabilities()->isDefinite()) {
            throw new Exception\IndefiniteQueryException($query);
        }

        return $query;
    }

    public function getAddressableQuery(QueryInterface $query): QueryInterface
    {
        if (!$query->getCapabilities()->isAddressable()) {
            throw new Exception\QueryNotAddressableException($query);
        }

        return $query;
    }
}
