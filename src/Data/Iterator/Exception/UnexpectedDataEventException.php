<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Iterator\Exception;

use function get_class;
use LogicException;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Throwable;

final class UnexpectedDataEventException extends LogicException implements ExceptionInterface
{

    private $event;

    private $expectedClass;

    public function __construct(DataEventInterface $event, string $expectedClass, Throwable $previous = null)
    {
        $this->event = $event;
        $this->expectedClass = $expectedClass;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        $actualClass = get_class($this->event);

        return "Unexpected data event: {$actualClass} instead of {$this->expectedClass}";
    }

    public function getEvent(): DataEventInterface
    {
        return $this->event;
    }

    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }
}
