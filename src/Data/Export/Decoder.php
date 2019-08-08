<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Iterator;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class Decoder implements DecoderInterface
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    public function exportEvents(Iterator $eventIterator)
    {
        $value = $this->valueIteratorFactory->fetchValue($eventIterator);
        if ($value instanceof ScalarValueInterface) {
            return $value->getData();
        }

        if ($value instanceof ArrayValueInterface) {
            $result = [];
            $arrayIterator = $this->valueIteratorFactory->createArrayIterator($value->createEventIterator());
            foreach ($arrayIterator as $index => $element) {
                $result[$index] = $this->exportValue($element);
            }

            return $result;
        }

        if ($value instanceof ObjectValueInterface) {
            $result = (object) [];
            $objectIterator = $this->valueIteratorFactory->createObjectIterator($value->createEventIterator());
            foreach ($objectIterator as $name => $property) {
                $result->{$name} = $this->exportValue($property);
            }

            return $result;
        }

        throw new Exception\UnexpectedValueException($value);
    }

    public function exportValue(ValueInterface $value)
    {
        return $this->exportEvents($value->createEventIterator());
    }
}
