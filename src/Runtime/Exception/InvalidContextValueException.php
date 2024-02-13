<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Throwable;

final class InvalidContextValueException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly ValueInterface $value,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Invalid context value", previous: $previous);
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
