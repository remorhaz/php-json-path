<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator\DecodedJson\Event;

use Iterator;
use Remorhaz\JSON\Path\Iterator\Event\AfterArrayEventInterface;
use Remorhaz\JSON\Path\Iterator\PathInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;

final class AfterArrayEvent implements AfterArrayEventInterface
{

    private $iteratorFactory;

    public function __construct(ValueInterface $iteratorFactory)
    {
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * @return PathInterface
     */
    public function getPath(): PathInterface
    {
        return $this->iteratorFactory->getPath();
    }

    public function createIterator(): Iterator
    {
        return $this->iteratorFactory->createIterator();
    }
}
