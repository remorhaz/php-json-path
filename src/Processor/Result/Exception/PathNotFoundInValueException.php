<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Throwable;

final class PathNotFoundInValueException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private ValueInterface $value,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Path not found in value", 0, $previous);
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
