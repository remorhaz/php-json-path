<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

interface LiteralFactoryInterface
{

    public function createScalar(NodeValueListInterface $source, $value): ValueListInterface;

    public function createArray(NodeValueListInterface $source, ValueListInterface ...$values): ValueListInterface;
}
