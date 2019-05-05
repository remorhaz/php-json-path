<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;
use Remorhaz\JSON\Path\Iterator\Exception;

final class EventExporter
{

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
    }

    public function export(Iterator $iterator)
    {
        $value = $this->valueIterator->fetchValue($iterator);
        if ($value instanceof ScalarValueInterface) {
            return $value->getData();
        }

        if ($value instanceof ArrayValueInterface) {
            $result = [];
            foreach ($this->valueIterator->createArrayIterator($value->createIterator()) as $index => $element) {
                $result[$index] = $this->export($element->createIterator());
            }

            return $result;
        }

        if ($value instanceof ObjectValueInterface) {
            $result = (object) [];
            foreach ($this->valueIterator->createObjectIterator($value->createIterator()) as $name => $property) {
                $result->{$name} = $this->export($property->createIterator());
            }

            return $result;
        }

        throw new Exception\UnexpectedValueException($value);
    }
}
