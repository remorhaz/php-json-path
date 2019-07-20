<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Throwable;

final class MaxElementNotFoundException extends LogicException implements ExceptionInterface
{

    private $dataList;

    private $elements;

    /**
     * @param array $dataList
     * @param ScalarValueInterface[] $elements
     * @param Throwable|null $previous
     */
    public function __construct(array $dataList, array $elements, Throwable $previous = null)
    {
        $this->dataList = $dataList;
        $this->elements = $elements;
        parent::__construct("Max element not found", 0, $previous);
    }

    public function getDataList(): array
    {
        return $this->dataList;
    }

    /**
     * @return ScalarValueInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
