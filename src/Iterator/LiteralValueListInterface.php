<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Iterator;

interface LiteralValueListInterface extends ValueListInterface
{

    public function getLiteral(): LiteralValueInterface;
}
