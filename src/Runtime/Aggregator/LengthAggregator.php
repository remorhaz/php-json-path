<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Data\Value\ValueIteratorFactory;

final class LengthAggregator implements ValueAggregatorInterface
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactory $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    public function tryAggregate(ValueInterface $value): ?ValueInterface
    {
        $length = $this->findElementCount($value);

        return isset($length)
            ? new LiteralScalarValue($length)
            : null;
    }

    private function findElementCount(ValueInterface $value): ?int
    {
        if (!$value instanceof ArrayValueInterface) {
            return null;
        }

        $arrayIterator = $this
            ->valueIteratorFactory
            ->createArrayIterator($value->createIterator());
        $count = 0;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($arrayIterator as $element) {
            $count++;
        }

        return $count;
    }
}
