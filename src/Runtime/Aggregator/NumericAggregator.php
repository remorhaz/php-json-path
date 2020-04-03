<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_map;
use function is_float;
use function is_int;

abstract class NumericAggregator implements ValueAggregatorInterface
{

    abstract protected function aggregateNumericData(
        array $dataList,
        ScalarValueInterface ...$elements
    ): ?ValueInterface;

    final public function tryAggregate(ValueInterface $value): ?ValueInterface
    {
        $numericElements = $this->findNumericElements($value);
        if (empty($numericElements)) {
            return null;
        }

        return $this->aggregateNumericData(
            $this->getElementDataList(...$numericElements),
            ...$numericElements
        );
    }

    protected function findNumericElement(ValueInterface $element): ?ScalarValueInterface
    {
        if (!$element instanceof ScalarValueInterface) {
            return null;
        }
        $elementData = $element->getData();
        return is_int($elementData) || is_float($elementData)
            ? $element
            : null;
    }

    /**
     * @param ValueInterface $value
     * @return ScalarValueInterface[]
     */
    protected function findNumericElements(ValueInterface $value): array
    {
        $numericElements = [];
        if (!$value instanceof ArrayValueInterface) {
            return $numericElements;
        }
        foreach ($value->createChildIterator() as $element) {
            $numericElement = $this->findNumericElement($element);
            if (isset($numericElement)) {
                $numericElements[] = $numericElement;
            }
        }
        return $numericElements;
    }

    protected function getElementDataList(ScalarValueInterface ...$elements): array
    {
        return array_map([$this, 'getElementData'], $elements);
    }

    private function getElementData(ScalarValueInterface $element)
    {
        return $element->getData();
    }
}
