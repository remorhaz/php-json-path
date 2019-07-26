<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use function call_user_func;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Query implements QueryInterface
{

    private $callback;

    private $properties;

    public function __construct(callable $callback, QueryPropertiesInterface $properties)
    {
        $this->callback = $callback;
        $this->properties = $properties;
    }

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface
    {
        return call_user_func($this->callback, $runtime, $rootNode);
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isDefinite(): bool
    {
        return $this->properties->isDefinite();
    }

    public function getProperties(): QueryPropertiesInterface
    {
        return $this->properties;
    }
}
