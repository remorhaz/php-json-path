<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use function array_map;
use function is_float;
use function is_int;

abstract class NumericAggregator implements ValueAggregatorInterface
{

    private $valueIterator;

    abstract protected function aggregateNumericData(
        array $dataList,
        ScalarValueInterface ...$elements
    ): ?ValueInterface;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
    }

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
        $arrayIterator = $this
            ->valueIterator
            ->createArrayIterator($value->createIterator());
        foreach ($arrayIterator as $element) {
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