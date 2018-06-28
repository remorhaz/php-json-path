<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Exception;

class ListVariable extends AbstractVariable
{

    private $variableList;

    public function __construct(int $type, VariableInterface ...$variableList)
    {
        parent::__construct($type);
        $this->variableList = $variableList;
    }

    /**
     * @throws Exception
     */
    public function getData()
    {
        throw new Exception("List variable doesn't have it's own data");
    }

    public function isList(): bool
    {
        return true;
    }

    /**
     * @param VariableInterface $variable
     * @throws Exception
     */
    public function append(VariableInterface $variable)
    {
        if ($variable->getType() != $this->getType()) {
            throw new Exception("Unexpected variable type");
        }
        if ($variable->isList()) {
            throw new Exception("Only scalar variables can be appended to list");
        }
        $this->variableList[] = $variable;
    }

    public function getList(): array
    {
        return $this->variableList;
    }
}
