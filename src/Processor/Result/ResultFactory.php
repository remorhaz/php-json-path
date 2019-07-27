<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use function array_map;
use function count;
use Remorhaz\JSON\Data\Export\DecoderInterface;
use Remorhaz\JSON\Data\Export\EncoderInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Processor\PathEncoder;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class ResultFactory implements ResultFactoryInterface
{

    private $jsonEncoder;

    private $jsonDecoder;

    private $pathsEncoder;

    public function __construct(EncoderInterface $jsonEncoder, DecoderInterface $jsonDecoder, PathEncoder $pathEncoder)
    {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->pathsEncoder = $pathEncoder;
    }

    public function createSelectResult(ValueListInterface $values): SelectResultInterface
    {
        return new SelectResult($this->jsonEncoder, $this->jsonDecoder, ...$values->getValues());
    }

    public function createSelectOneResult(ValueListInterface $values): SelectOneResultInterface
    {
        $value = $this->findSingleValue($values);

        return isset($value)
            ? new ExistingSelectOneResult(
                $this->jsonEncoder,
                $this->jsonDecoder,
                $values->getValue(0)
            )
            : new NonExistingSelectOneResult;
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

        return isset($values)
            ? new ExistingSelectOnePathResult($this->pathsEncoder, $this->getValuePath($value))
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
}
