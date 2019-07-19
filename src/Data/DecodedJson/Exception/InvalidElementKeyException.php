<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\DecodedJson\Exception;

use Remorhaz\JSON\Data\Exception\ExceptionInterface;
use Remorhaz\JSON\Data\PathInterface;
use Throwable;

class InvalidElementKeyException extends \RuntimeException implements ExceptionInterface
{

    private $key;

    private $path;

    public function __construct($key, PathInterface $path, Throwable $previous = null)
    {
        $this->key = $key;
        $this->path = $path;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid element key in decoded JSON";
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function getKey()
    {
        return $this->key;
    }
}
