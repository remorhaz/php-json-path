<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export\Exception;

use DomainException;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Throwable;

final class UnexpectedValueException extends DomainException implements ExceptionInterface
{

    private $value;

    public function __construct(ValueInterface $value, Throwable $previous = null)
    {
        $this->value = $value;
        parent::__construct("Unexpected value", 0, $previous);
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
