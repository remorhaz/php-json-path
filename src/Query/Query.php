<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use function call_user_func;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Query implements QueryInterface
{

    private $source;

    private $callback;

    private $properties;

    public function __construct(string $source, callable $callback, QueryCapabilitiesInterface $properties)
    {
        $this->source = $source;
        $this->callback = $callback;
        $this->properties = $properties;
    }

    public function __invoke(RuntimeInterface $runtime, NodeValueInterface $rootNode): ValueListInterface
    {
        return call_user_func($this->callback, $runtime, $rootNode);
    }

    /**
     * @return QueryCapabilitiesInterface
     * @deprecated
     */
    public function getProperties(): QueryCapabilitiesInterface
    {
        return $this->getCapabilities();
    }

    public function getCapabilities(): QueryCapabilitiesInterface
    {
        return $this->properties;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
