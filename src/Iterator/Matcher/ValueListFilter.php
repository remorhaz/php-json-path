<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\Matcher;

use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

class ValueListFilter implements ValueListFilterInterface
{

    private $filterResults;

    public function __construct(ValueInterface ...$filterResults)
    {
        $this->filterResults = $filterResults;
    }

    /**
     * @param ValueInterface ...$values
     * @return ValueInterface[]
     */
    public function filterValues(ValueInterface ...$values): array
    {
        if (\array_keys($this->filterResults) !== \array_keys($values)) {
            throw new Exception\InvalidValuesException();
        }

        $result = [];
        foreach ($this->filterResults as $index => $filterValue) {
            $exportedValue = (new EventExporter(new Fetcher))->export($filterValue->getIterator());
            if (is_int($exportedValue) && $exportedValue != 0) {
                $result[$index] = $values[$index];
            }
        }
        return $result;
    }
}
