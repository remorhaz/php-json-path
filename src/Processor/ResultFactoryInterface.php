<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Path\Value\ValueListInterface;

interface ResultFactoryInterface
{

    public function createSelectResult(ValueListInterface $values): SelectResultInterface;

    public function createSelectOneResult(ValueListInterface $values): SelectOneResultInterface;

    public function createSelectPathsResult(ValueListInterface $values): SelectPathsResultInterface;

    public function createSelectOnePathResult(ValueListInterface $values): SelectOnePathResultInterface;
}
