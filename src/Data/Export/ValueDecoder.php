<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class ValueDecoder implements ValueDecoderInterface
{

    public function exportValue(ValueInterface $value)
    {
        if ($value instanceof ScalarValueInterface) {
            return $value->getData();
        }

        if ($value instanceof ArrayValueInterface) {
            $result = [];
            foreach ($value->createChildIterator() as $index => $element) {
                $result[$index] = $this->exportValue($element);
            }

            return $result;
        }

        if ($value instanceof ObjectValueInterface) {
            $result = (object) [];
            foreach ($value->createChildIterator() as $name => $property) {
                $result->{$name} = $this->exportValue($property);
            }

            return $result;
        }

        throw new Exception\UnexpectedValueException($value);
    }
}
