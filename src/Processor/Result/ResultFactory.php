<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Path\Processor\PathEncoderInterface;
use function array_map;
use function count;
use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class ResultFactory implements ResultFactoryInterface
{

    private $jsonEncoder;

    private $jsonDecoder;

    private $pathsEncoder;

    public function __construct(
        ValueEncoderInterface $jsonEncoder,
        ValueDecoderInterface $jsonDecoder,
        PathEncoderInterface $pathEncoder
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->pathsEncoder = $pathEncoder;
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
            ...array_map([$this, 'getValuePath'], $values->getValues())
        );
    }

    private function getValuePath(ValueInterface $value): PathInterface
    {
        if (!$value instanceof NodeValueInterface) {
            throw new Exception\PathNotFoundInValueException($value);
        }

        return $value->getPath();
    }

    public function createSelectOnePathResult(ValueListInterface $values): SelectOnePathResultInterface
    {
        $value = $this->findSingleValue($values);
        $path = isset($value)
            ? $this->getValuePath($value)
            : null;

        return isset($path)
            ? new ExistingSelectOnePathResult($this->pathsEncoder, $path)
            : new NonExistingSelectOnePathResult;
    }

    private function findSingleValue(ValueListInterface $values): ?ValueInterface
    {
        switch (count($values->getValues())) {
            case 0:
                return null;

            case 1:
                return $values->getValue(0);
        }

        throw new Exception\MoreThanOneValueInListException($values);
    }

    public function createValueResult(?ValueInterface $value): ValueResultInterface
    {
        return isset($value)
            ? new ExistingValueResult($this->jsonEncoder, $this->jsonDecoder, $value)
            : new NonExistingValueResult;
    }
}
