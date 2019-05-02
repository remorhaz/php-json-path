<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Remorhaz\JSON\Path\Iterator\Event\BeforeArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class BeforeArrayEvent implements BeforeArrayEventInterface
{

    private $value;

    public function __construct(NodeValueInterface $value)
    {
        $this->value = $value;
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
