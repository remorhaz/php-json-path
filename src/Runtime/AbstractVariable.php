<?php

namespace Remorhaz\JSON\Path\Runtime;

use Remorhaz\JSON\Path\Exception;

abstract class AbstractVariable implements VariableInterface
{

    private $type;

    private $forkId;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }


    public function isForked(): bool
    {
        return isset($this->forkId);
    }

    public function setForkId(int $id)
    {
        $this->forkId = $id;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getForkId(): int
    {
        if (!$this->isForked()) {
            throw new Exception("Variable is not forked");
        }
        return $this->forkId;
    }
}
