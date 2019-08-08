<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Aggregator;

use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use function iterator_count;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class LengthAggregator implements ValueAggregatorInterface
{

    private $valueIteratorFactory;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory)
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
            ->createArrayIterator($value->createEventIterator());

        return iterator_count($arrayIterator);
    }
}
