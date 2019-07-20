<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use DomainException;
use Remorhaz\JSON\Data\Value\DataAwareInterface;
use Throwable;

final class InvalidScalarDataException extends DomainException implements
    ExceptionInterface,
    DataAwareInterface
{

    private $data;

    public function __construct($data, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Invalid scalar data";
    }

    public function getData()
    {
        return $this->data;
    }
}
