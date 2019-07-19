<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\ScalarValueInterface;
use Remorhaz\JSON\Data\ValueInterface;

abstract class UniqueNumericAggregator extends NumericAggregator
{

    /**
     * @param ValueInterface $value
     * @return ScalarValueInterface[]
     */
    protected function findNumericElements(ValueInterface $value): array
    {
        $elements = [];
        $uniqueDataList = [];
        foreach (parent::findNumericElements($value) as $element) {
            if (in_array($element->getData(), $uniqueDataList, true)) {
                continue;
            }
            $uniqueDataList[] = $element->getData();
            $elements[] = $element;
        }

        return $elements;
    }
}
