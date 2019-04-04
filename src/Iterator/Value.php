<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

use Iterator;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIterator;

final class Value implements ValueInterface
{

    private $value;

    private $path;

    public static function createInteger(int $value): ValueInterface
    {
        $path = Path::createEmpty();
        return new self(EventIterator::create($value, $path), $path);
    }

    public function __construct(Iterator $value, PathInterface $path)
    {
        $this->value = $value;
        $this->path = $path;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        return $this->value;
    }
}
