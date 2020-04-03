<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator\Exception;

use DomainException;
use Throwable;

final class AggregateFunctionNotFoundException extends DomainException implements ExceptionInterface
{

    private $name;

    public function __construct(string $name, Throwable $previous = null)
    {
        $this->name = $name;
        parent::__construct("Aggregate function not found: {$this->name}", 0, $previous);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
