<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface QueryValidatorInterface
{

    public function getDefiniteQuery(QueryInterface $query): QueryInterface;

    public function getPathQuery(QueryInterface $query): QueryInterface;

    public function getDefinitePathQuery(QueryInterface $query): QueryInterface;
}
