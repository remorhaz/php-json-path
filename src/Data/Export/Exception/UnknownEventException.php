<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export\Exception;

use LogicException;
use Remorhaz\JSON\Data\Event\EventInterface;
use Throwable;

final class UnknownEventException extends LogicException implements ExceptionInterface
{

    private $event;

    public function __construct(EventInterface $event, Throwable $previous = null)
    {
        $this->event = $event;
        parent::__construct("Unknown event", 0, $previous);
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }
}
