<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Event;

use Remorhaz\JSON\Path\Iterator\LiteralScalarValue;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class LiteralScalarEvent implements ScalarEventInterface
{

    private $data;

    private $value;

    public function __construct($data)
    {
        $this->value = new LiteralScalarValue($data);
        $this->data = $data;
    }

    public function getValue(): ValueInterface
    {
        return $this->value;
    }

    public function getData()
    {
        return $this->data;
    }
}