<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use Generator;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use Remorhaz\JSON\Path\Exception;
use Remorhaz\JSON\Data\Value\ArrayValueInterface;
use Remorhaz\JSON\Data\Value\ObjectValueInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Data\Value\ValueIteratorFactoryInterface;
use stdClass;
use Throwable;

final class SelectResult implements SelectResultInterface
{

    private $valueIteratorFactory;

    private $values;

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory, ValueInterface ...$values)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
        $this->values = $values;
    }

    public function decode(): array
    {
        return array_map([$this, 'exportDecodedValue'], $this->values);
    }

    public function asJson(): array
    {
        return array_map([$this, 'exportJsonValue'], $this->values);
    }

    private function exportJsonValue(ValueInterface $value): string
    {
        $decodedValue = $this->exportDecodedValue($value);

        try {
            return json_encode(
                $decodedValue,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
            );
        } catch (Throwable $e) {
            throw new Exception\JsonExportFailedException($value, $e);
        }
    }

    private function exportDecodedValue(ValueInterface $value)
    {
        switch (true) {
            case $value instanceof ScalarValueInterface:
                return $this->exportDecodedScalar($value);

            case $value instanceof ArrayValueInterface:
                return $this->exportDecodedArray($value);

            case $value instanceof ObjectValueInterface:
                return $this->exportDecodedObject($value);
        }

        throw new Exception\ValueNotDecodedException($value);
    }

    private function exportDecodedScalar(ScalarValueInterface $value)
    {
        return $value->getData();
    }

    private function exportDecodedArray(ArrayValueInterface $value): array
    {
        /** @var Generator|ValueInterface[] $arrayValueIterator */
        $arrayValueIterator = $this
            ->valueIteratorFactory
            ->createArrayIterator($value->createIterator());
        $array = [];
        foreach ($arrayValueIterator as $index => $element) {
            $array[$index] = $this->exportDecodedValue($element);
        }

        return $array;
    }

    private function exportDecodedObject(ObjectValueInterface $value): stdClass
    {
        /** @var Generator|ValueInterface[] $objectValueIterator */
        $objectValueIterator = $this
            ->valueIteratorFactory
            ->createObjectIterator($value->createIterator());
        $object = (object) [];
        foreach ($objectValueIterator as $name => $property) {
            $object->{$name} = $this->exportDecodedValue($property);
        }

        return $object;
    }
}
