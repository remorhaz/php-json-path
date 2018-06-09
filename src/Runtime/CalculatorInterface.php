<?php

namespace Remorhaz\JSON\Path\Runtime;

interface CalculatorInterface
{

    public function equals(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface;

    public function not(VariableInterface $value): VariableInterface;

    public function less(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface;

    public function lessOrEqual(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface;

    public function greater(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface;

    public function greaterOrEqual(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface;

    public function in(VariableInterface $value, VariableInterface $valueList): VariableInterface;

    public function matches(VariableInterface $value, string $regExp): VariableInterface;
}
