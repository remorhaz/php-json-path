<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\DecodedJson\Exception;

use Remorhaz\JSON\Data\Exception\ExceptionInterface;
use Remorhaz\JSON\Data\Exception\PathAwareExceptionTrait;
use Remorhaz\JSON\Data\PathInterface;
use RuntimeException;
use Throwable;

class InvalidNodeDataException extends RuntimeException implements ExceptionInterface
{

    use PathAwareExceptionTrait;

    private $data;

    public function __construct($data, PathInterface $path, Throwable $previous = null)
    {
        $this->data = $data;
        $this->path = $path;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid data in decoded JSON at {$this->buildPath()}";
    }

    public function getData()
    {
        return $this->data;
    }
}
