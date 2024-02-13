<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Path\Value\LiteralValueInterface;
use Throwable;

final class LiteralEvaluationFailedException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly LiteralValueInterface $literal,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Failed to evaluate literal value", previous: $previous);
    }

    public function getLiteral(): LiteralValueInterface
    {
        return $this->literal;
    }
}
