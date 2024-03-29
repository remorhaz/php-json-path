<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result\Exception;

use Remorhaz\JSON\Path\Value\ValueListInterface;
use RuntimeException;
use Throwable;

final class MoreThanOneValueInListException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        private readonly ValueListInterface $values,
        ?Throwable $previous = null,
    ) {
        parent::__construct("More than 1 value in list", previous: $previous);
    }

    public function getValues(): ValueListInterface
    {
        return $this->values;
    }
}
