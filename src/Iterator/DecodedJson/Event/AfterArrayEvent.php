<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class AfterArrayEvent implements AfterArrayEventInterface
{

    private $value;

    public function __construct(NodeValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @return ValueInterface
     */
    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
