<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator\Exception;

use LogicException;
use Remorhaz\JSON\Data\Path\PathInterface;
use Throwable;

final class ReplaceAtNestedPathsException extends LogicException implements ExceptionInterface
{
    public function __construct(
        private readonly PathInterface $parentPath,
        private readonly PathInterface $nestedPath,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Attempt of replacing value at nested paths", previous: $previous);
    }

    public function getParentPath(): PathInterface
    {
        return $this->parentPath;
    }

    public function getNestedPath(): PathInterface
    {
        return $this->nestedPath;
    }
}
