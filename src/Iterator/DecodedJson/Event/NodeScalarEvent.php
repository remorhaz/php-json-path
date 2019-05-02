<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValue;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEventInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class NodeScalarEvent implements ScalarEventInterface
{

    private $data;

    private $value;

    public function __construct($data, PathInterface $path)
    {
        $this->value = new NodeValue($data, $path);
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