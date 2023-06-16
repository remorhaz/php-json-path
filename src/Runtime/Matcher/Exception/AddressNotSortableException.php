<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher\Exception;

use DomainException;
use Throwable;

final class AddressNotSortableException extends DomainException implements ExceptionInterface
{
    public function __construct(
        private int|string $address,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Index/property is not sortable: {$this->address}", 0, $previous);
    }

    public function getAddress(): int|string
    {
        return $this->address;
    }
}
