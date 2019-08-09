<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\DecodedJson;

use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactoryInterface;
use function is_array;
use function is_scalar;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Path\PathInterface;
use stdClass;

final class NodeValueFactory implements NodeValueFactoryInterface
{

    private $valueIteratorFactory;

    public static function create(): NodeValueFactoryInterface
    {
        return new self(new ValueIteratorFactory);
    }

    public function __construct(ValueIteratorFactoryInterface $valueIteratorFactory)
    {
        $this->valueIteratorFactory = $valueIteratorFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param array|bool|float|int|stdClass|string|null $data
     * @param PathInterface|null $path
     * @return NodeValueInterface
     */
    public function createValue($data, ?PathInterface $path = null): NodeValueInterface
    {
        if (!isset($path)) {
            $path = new Path;
        }
        if (null === $data || is_scalar($data)) {
            return new NodeScalarValue($data, $path);
        }

        if (is_array($data)) {
            return new NodeArrayValue($data, $path, $this, $this->valueIteratorFactory);
        }

        if ($data instanceof stdClass) {
            return new NodeObjectValue($data, $path, $this, $this->valueIteratorFactory);
        }

        throw new Exception\InvalidNodeDataException($data, $path);
    }
}
