<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

final class LengthAggregator implements ValueAggregatorInterface
{

    private $valueIterator;

    public function __construct(ValueIterator $valueIterator)
    {
        $this->valueIterator = $valueIterator;
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
            ->valueIterator
            ->createArrayIterator($value->createIterator());
        $count = 0;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($arrayIterator as $element) {
            $count++;
        }

        return $count;
    }
}
