<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result\Exception;

use Remorhaz\JSON\Path\Value\ValueListInterface;
use RuntimeException;
use Throwable;

final class MoreThanOneValueInListException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private ValueListInterface $values,
        ?Throwable $previous = null,
    ) {
        parent::__construct("More than 1 value in list", 0, $previous);
    }

    public function getValues(): ValueListInterface
    {
        return $this->values;
    }
}
