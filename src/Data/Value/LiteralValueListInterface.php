<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

interface LiteralValueListInterface extends ValueListInterface
{

    public function getLiteral(): LiteralValueInterface;
}
