<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value;

interface LiteralValueListInterface extends ValueListInterface
{
    public function getLiteral(): LiteralValueInterface;

    /**
     * @return list<LiteralValueInterface>
     */
    public function getValues(): array;
}
