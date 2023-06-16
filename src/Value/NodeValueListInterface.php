<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface NodeValueListInterface extends ValueListInterface
{
    /**
     * @return list<NodeValueInterface>
     */
    public function getValues(): array;
}
