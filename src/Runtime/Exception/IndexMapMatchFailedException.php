<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Throwable;

final class IndexMapMatchFailedException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly ValueListInterface $leftValues,
        private readonly ValueListInterface $rightValues,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Index map match failed", previous: $previous);
    }

    public function getLeftValues(): ValueListInterface
    {
        return $this->leftValues;
    }

    public function getRightValues(): ValueListInterface
    {
        return $this->rightValues;
    }
}
