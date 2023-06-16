<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface QueryFactoryInterface
{
    public function createQuery(string $path): QueryInterface;
}
