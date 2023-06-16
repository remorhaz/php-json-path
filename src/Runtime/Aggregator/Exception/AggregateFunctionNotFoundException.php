<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator\Exception;

use DomainException;
use Throwable;

final class AggregateFunctionNotFoundException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private string $name,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Aggregate function not found: $this->name", 0, $previous);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
