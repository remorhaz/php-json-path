<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

interface QueryInterface
{

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface;

    /**
     * @return bool
     * @deprecated
     */
    public function isDefinite(): bool;

    public function getProperties(): QueryPropertiesInterface;
}
