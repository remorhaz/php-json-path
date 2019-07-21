<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Throwable;

final class InvalidContextValueException extends LogicException implements ExceptionInterface
{

    private $value;

    public function __construct(ValueInterface $value, Throwable $previous = null)
    {
        $this->value = $value;
        parent::__construct("Invalid context value", 0, $previous);
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
