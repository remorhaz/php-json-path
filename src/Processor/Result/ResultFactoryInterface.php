<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

interface ResultFactoryInterface
{

    public function createSelectResult(ValueListInterface $values): SelectResultInterface;

    public function createSelectOneResult(ValueListInterface $values): ValueResultInterface;

    public function createSelectPathsResult(ValueListInterface $values): SelectPathsResultInterface;

    public function createSelectOnePathResult(ValueListInterface $values): SelectOnePathResultInterface;

    public function createValueResult(?ValueInterface $value): ValueResultInterface;
}
