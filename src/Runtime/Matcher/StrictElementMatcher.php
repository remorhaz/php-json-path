<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function in_array;
use Remorhaz\JSON\Data\Value\ValueListInterface;

final class StrictElementMatcher implements ChildMatcherInterface
{

    private $indice;

    /**
     * @param ValueListInterface $valueList
     * @param array[] $indexLists
     * @return self[]
     */
    public static function populate(ValueListInterface $valueList, array ...$indexLists): array
    {
        if (\array_keys($indexLists) !== $valueList->getIndexMap()->getInnerIndice()) {
            throw new Exception\InvalidIndexListException();
        }

        $result = [];
        foreach ($indexLists as $indice) {
            $result[] = new self(...$indice);
        }

        return $result;
    }

    public function __construct(int ...$indice)
    {
        $this->indice = $indice;
    }

    public function match($address): bool
    {
        return in_array($address, $this->indice, true);
    }
}
