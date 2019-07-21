<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Throwable;

final class UnexpectedNodeValueFetchedException extends LogicException implements ExceptionInterface
{

    private $value;

    public function __construct(NodeValueInterface $value, Throwable $previous = null)
    {
        $this->value = $value;
        parent::__construct("Unexpected node value fetched", 0, $previous);
    }

    public function getValue(): NodeValueInterface
    {
        return $this->value;
    }
}
