<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

use Iterator;
use Remorhaz\JSON\Data\Exception;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;

final class EventExporter
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    public function export(Iterator $iterator)
    {
        $value = $this->valueIteratorFactory->fetchValue($iterator);
        if ($value instanceof ScalarValueInterface) {
            return $value->getData();
        }

        if ($value instanceof ArrayValueInterface) {
            $result = [];
            $arrayIterator = $this->valueIteratorFactory->createArrayIterator($value->createIterator());
            foreach ($arrayIterator as $index => $element) {
                $result[$index] = $this->export($element->createIterator());
            }

            return $result;
        }

        if ($value instanceof ObjectValueInterface) {
            $result = (object) [];
            $objectIterator = $this->valueIteratorFactory->createObjectIterator($value->createIterator());
            foreach ($objectIterator as $name => $property) {
                $result->{$name} = $this->export($property->createIterator());
            }

            return $result;
        }

        throw new Exception\UnexpectedValueException($value);
    }
}
