<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher\Exception;

use DomainException;
use Throwable;

final class AddressNotSortableException extends DomainException implements ExceptionInterface
{

    private $address;

    /**
     * @param int|string     $address
     * @param Throwable|null $previous
     */
    public function __construct($address, Throwable $previous = null)
    {
        $this->address = $address;
        parent::__construct("Index/property is not sortable: {$this->address}", 0, $previous);
    }

    /**
     * @return int|string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
