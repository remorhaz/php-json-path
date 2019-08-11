<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event\Exception;

use LogicException;
use Throwable;

final class UnexpectedEntityException extends LogicException implements ExceptionInterface
{

    private $entity;

    public function __construct($entity, Throwable $previous = null)
    {
        $this->entity = $entity;
        parent::__construct("Invalid entity", 0, $previous);
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
