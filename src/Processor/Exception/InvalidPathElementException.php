<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Exception;

use DomainException;
use Throwable;

use function gettype;

final class InvalidPathElementException extends DomainException implements ExceptionInterface
{

    private $pathElement;

    public function __construct($pathElement, Throwable $previous = null)
    {
        $this->pathElement = $pathElement;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        $type = gettype($this->pathElement);

        return "Invalid path element type: {$type}";
    }

    public function getPathElement()
    {
        return $this->pathElement;
    }
}
