<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime\Matcher;

use function array_keys;
use function in_array;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class StrictPropertyMatcher implements ChildMatcherInterface
{

    private $properties;

    /**
     * @param ValueListInterface $valueList
     * @param array[] $propertyLists
     * @return self[]
     */
    public static function populate(ValueListInterface $valueList, array ...$propertyLists): array
    {
        if (array_keys($propertyLists) !== $valueList->getIndexMap()->getInnerIndice()) {
            throw new Exception\InvalidIndexListException();
        }

        $result = [];
        foreach ($propertyLists as $properties) {
            $result[] = new self(...$properties);
        }

        return $result;
    }

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }

    public function match($address): bool
    {
        return in_array($address, $this->properties, true);
    }
}
