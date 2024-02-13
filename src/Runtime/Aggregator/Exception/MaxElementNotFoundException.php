<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator\Exception;

use LogicException;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Throwable;

final class MaxElementNotFoundException extends LogicException implements ExceptionInterface
{
    /**
     * @param array $dataList
     * @param list<ScalarValueInterface> $elements
     * @param Throwable|null $previous
     */
    public function __construct(
        private readonly array $dataList,
        private readonly array $elements,
        ?Throwable $previous = null,
    ) {

        parent::__construct("Max element not found", previous: $previous);
    }

    public function getDataList(): array
    {
        return $this->dataList;
    }

    /**
     * @return list<ScalarValueInterface>
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
