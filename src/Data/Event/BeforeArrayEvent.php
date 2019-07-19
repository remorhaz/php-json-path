<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\ArrayValueInterface;
use Remorhaz\JSON\Data\ValueInterface;

final class BeforeArrayEvent implements BeforeArrayEventInterface
{

    private $value;

    public function __construct(ArrayValueInterface $value)
    {
        $this->value = $value;
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
