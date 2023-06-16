<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Throwable;

final class UnexpectedNodeValueFetchedException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private NodeValueInterface $value,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Unexpected node value fetched", 0, $previous);
    }

    public function getValue(): NodeValueInterface
    {
        return $this->value;
    }
}
