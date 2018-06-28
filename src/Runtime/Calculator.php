<?php

namespace Remorhaz\JSON\Path\Runtime;

class Calculator implements CalculatorInterface
{

    public function equals(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface
    {
    }

    public function greater(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface
    {
    }

    public function greaterOrEqual(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface
    {
    }

    public function in(VariableInterface $value, VariableInterface $valueList): VariableInterface
    {
    }

    public function less(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface
    {
    }

    public function lessOrEqual(VariableInterface $firstValue, VariableInterface $secondValue): VariableInterface
    {
    }

    public function matches(VariableInterface $value, string $regExp): VariableInterface
    {
    }

    public function not(VariableInterface $value): VariableInterface
    {
    }
}
