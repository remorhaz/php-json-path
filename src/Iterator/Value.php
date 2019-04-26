<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;

final class Value implements ValueInterface
{

    private $iteratorFactory;

    public static function createInteger(int $value): ValueInterface
    {
        return new self(new EventIteratorFactory($value, Path::createEmpty()));
    }

    public static function createArray(ValueInterface ...$values): ValueInterface
    {

    }

    public function __construct(ValueInterface $iteratorFactory)
    {
        $this->iteratorFactory = $iteratorFactory;
    }

    public function getPath(): PathInterface
    {
        return $this->iteratorFactory->getPath();
    }

    /**
     * @return Iterator
     */
    public function createIterator(): Iterator
    {
        return $this->iteratorFactory->createIterator();
    }
}
