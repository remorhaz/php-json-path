<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Throwable;

final class IndexMapMatchFailedException extends LogicException implements ExceptionInterface
{

    private $leftValues;

    private $rightValues;

    public function __construct(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues,
        Throwable $previous = null
    ) {
        $this->leftValues = $leftValues;
        $this->rightValues = $rightValues;
        parent::__construct("Index map match failed", 0, $previous);
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
