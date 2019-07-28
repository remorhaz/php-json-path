<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Value\EvaluatedValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

interface EvaluatorInterface
{

    public function logicalOr(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function logicalAnd(
        EvaluatedValueListInterface $leftValues,
        EvaluatedValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function logicalNot(EvaluatedValueListInterface $values): EvaluatedValueListInterface;

    public function isEqual(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function isGreater(
        ValueListInterface $leftValues,
        ValueListInterface $rightValues
    ): EvaluatedValueListInterface;

    public function isRegExp(string $regExp, ValueListInterface $values): EvaluatedValueListInterface;

    public function evaluate(
        ValueListInterface $sourceValues,
        ValueListInterface $resultValues
    ): EvaluatedValueListInterface;

    public function aggregate(string $functionName, ValueListInterface $values): ValueListInterface;
}
