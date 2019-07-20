<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator\Exception;

use RuntimeException;
use Throwable;

final class InvalidDataEventException extends RuntimeException implements ExceptionInterface
{

    private $event;

    public function __construct($event, Throwable $previous = null)
    {
        $this->event = $event;
        parent::__construct("Invalid data event", 0, $previous);
    }

    public function getEvent()
    {
        return $this->event;
    }
}
