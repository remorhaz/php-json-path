<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Query\QueryInterface;

interface QueryValidatorInterface
{

    public function getDefiniteQuery(QueryInterface $query): QueryInterface;

    public function getPathQuery(QueryInterface $query): QueryInterface;
}
