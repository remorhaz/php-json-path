<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\DecodedJson\Exception;

use function gettype;
use function is_int;
use function is_string;
use Remorhaz\JSON\Data\Exception\ExceptionInterface;
use Remorhaz\JSON\Data\Exception\PathAwareExceptionTrait;
use Remorhaz\JSON\Data\PathAwareInterface;
use Remorhaz\JSON\Data\PathInterface;
use RuntimeException;
use Throwable;

class InvalidElementKeyException extends RuntimeException implements ExceptionInterface, PathAwareInterface
{

    use PathAwareExceptionTrait;

    private $key;

    /**
     * @param mixed $key
     * @param PathInterface $path
     * @param Throwable|null $previous
     */
    public function __construct($key, PathInterface $path, Throwable $previous = null)
    {
        $this->key = $key;
        $this->path = $path;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid element key in decoded JSON: {$this->buildKey()} at {$this->buildPath()}";
    }

    public function getKey()
    {
        return $this->key;
    }

    private function buildKey(): string
    {
        if (is_string($this->key)) {
            return $this->key;
        }

        if (is_int($this->key)) {
            return (string) $this->key;
        }

        $type = gettype($this->key);

        return "<{$type}>";
    }
}
