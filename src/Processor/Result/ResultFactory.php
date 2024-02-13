<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

use function array_map;
use function count;

final class ResultFactory implements ResultFactoryInterface
{
    public function __construct(
        private readonly ValueEncoderInterface $jsonEncoder,
        private readonly ValueDecoderInterface $jsonDecoder,
        private readonly PathEncoderInterface $pathsEncoder,
    ) {
    }

    public function createSelectResult(ValueListInterface $values): SelectResultInterface
    {
        return new SelectResult($this->jsonEncoder, $this->jsonDecoder, ...$values->getValues());
    }

    public function createSelectOneResult(ValueListInterface $values): ValueResultInterface
    {
        return $this->createValueResult($this->findSingleValue($values));
    }

    public function createSelectPathsResult(ValueListInterface $values): SelectPathsResultInterface
    {
        return new SelectPathsResult(
            $this->pathsEncoder,
            ...array_map($this->getValuePath(...), $values->getValues()),
        );
    }

    private function getValuePath(ValueInterface $value): PathInterface
    {
        return $value instanceof NodeValueInterface
            ? $value->getPath()
            : throw new Exception\PathNotFoundInValueException($value);
    }

    public function createSelectOnePathResult(ValueListInterface $values): SelectOnePathResultInterface
    {
        $value = $this->findSingleValue($values);
        $path = isset($value)
            ? $this->getValuePath($value)
            : null;

        return isset($path)
            ? new ExistingSelectOnePathResult($this->pathsEncoder, $path)
            : new NonExistingSelectOnePathResult();
    }

    private function findSingleValue(ValueListInterface $values): ?ValueInterface
    {
        return match (count($values->getValues())) {
            0 => null,
            1 => $values->getValue(0),
            default => throw new Exception\MoreThanOneValueInListException($values),
        };
    }

    public function createValueResult(?ValueInterface $value): ValueResultInterface
    {
        return isset($value)
            ? new ExistingValueResult($this->jsonEncoder, $this->jsonDecoder, $value)
            : new NonExistingValueResult();
    }
}
