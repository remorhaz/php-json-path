<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use function array_map;
use function is_float;
use function is_int;
use Remorhaz\JSON\Path\Iterator\ArrayValueInterface;
use Remorhaz\JSON\Path\Iterator\ScalarValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;

abstract class NumericAggregator implements ValueAggregatorInterface
{

    private $valueIteratorFactory;

    abstract protected function aggregateNumericData(
        array $dataList,
        ScalarValueInterface ...$elements
    ): ?ValueInterface;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
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
            ->valueIteratorFactory
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
