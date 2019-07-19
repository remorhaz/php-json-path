<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

interface LiteralValueListInterface extends ValueListInterface
{

    public function getLiteral(): LiteralValueInterface;
}
