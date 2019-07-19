<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\ScalarValueInterface;
use Remorhaz\JSON\Data\ValueInterface;

final class ScalarEvent implements ScalarEventInterface
{

    private $value;

    public function __construct(ScalarValueInterface $value)
    {
        $this->value = $value;
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }
}
