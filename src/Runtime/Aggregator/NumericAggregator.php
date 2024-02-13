<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_filter;
use function array_map;
use function array_values;
use function is_float;
use function is_int;

abstract class NumericAggregator implements ValueAggregatorInterface
{
    /**
     * @param list<int|float> $dataList
     * @param ScalarValueInterface ...$elements
     * @return ValueInterface|null
     */
    abstract protected function aggregateNumericData(
        array $dataList,
        ScalarValueInterface ...$elements,
    ): ?ValueInterface;

    final public function tryAggregate(ValueInterface $value): ?ValueInterface
    {
        $numericElements = $this->findNumericElements($value);

        return empty($numericElements)
            ? null
            : $this->aggregateNumericData(
                $this->getElementDataList(...$numericElements),
                ...$numericElements,
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
     * @return list<ScalarValueInterface>
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

    /**
     * @param ScalarValueInterface ...$elements
     * @return list<int|float>
     */
    protected function getElementDataList(ScalarValueInterface ...$elements): array
    {
        return array_values(
            array_filter(
                array_map($this->findElementData(...), $elements),
            ),
        );
    }

    private function findElementData(ScalarValueInterface $element): int|float|null
    {
        $data = $element->getData();

        return is_int($data) || is_float($data)
            ? $data
            : null;
    }
}
