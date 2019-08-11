<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event\Exception;

use LogicException;
use Throwable;

final class InvalidScalarDataException extends LogicException implements ExceptionInterface
{

    private $data;

    public function __construct($data, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct("Invalid scalar data", 0, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}
