<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\ArrayValueInterface;
use Remorhaz\JSON\Data\ValueInterface;

final class AfterArrayEvent implements AfterArrayEventInterface
{

    private $value;

    public function __construct(ArrayValueInterface $value)
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
