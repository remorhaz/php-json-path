<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Exception;

use Remorhaz\JSON\Path\Iterator\Exception\ExceptionInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Throwable;

class InvalidDataException extends \RuntimeException implements ExceptionInterface
{

    private $data;

    private $path;

    public function __construct($data, PathInterface $path, Throwable $previous = null)
    {
        $this->data = $data;
        $this->path = $path;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid data in decoded JSON";
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    public function getData()
    {
        return $this->data;
    }
}
