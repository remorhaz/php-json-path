<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ValueListInterface;
use Throwable;

final class IndexMapMatchFailedException extends LogicException implements ExceptionInterface
{

    private $descendant;

    private $ancestor;

    public function __construct(
        ValueListInterface $descendant,
        ValueListInterface $ancestor,
        Throwable $previous = null
    ) {
        $this->descendant = $descendant;
        $this->ancestor = $ancestor;
        parent::__construct($this->buildMessage(), 0, $previous);
    }

    private function buildMessage(): string
    {
        return "Index map match failed";
    }

    public function getDescendant(): ValueListInterface
    {
        return $this->descendant;
    }

    public function getAncestor(): ValueListInterface
    {
        return $this->ancestor;
    }
}
