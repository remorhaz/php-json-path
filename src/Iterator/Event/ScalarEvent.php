<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

use Remorhaz\JSON\Path\Iterator\ScalarValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

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
