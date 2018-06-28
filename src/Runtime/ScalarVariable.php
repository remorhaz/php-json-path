<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Exception;

class ScalarVariable extends AbstractVariable
{

    private $data;

    public function __construct(int $type, $data)
    {
        parent::__construct($type);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function isList(): bool
    {
        return false;
    }

    /**
     * @param VariableInterface $variable
     * @throws Exception
     */
    public function append(VariableInterface $variable)
    {
        throw new Exception("Append operation is not supported for scalar variables");
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getList(): array
    {
        throw new Exception("Scalar variable accessed as list");
    }
}
