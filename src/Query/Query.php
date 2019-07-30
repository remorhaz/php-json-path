<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use function call_user_func;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

final class Query implements QueryInterface
{

    private $source;

    private $callback;

    private $properties;

    public function __construct(string $source, callable $callback, CapabilitiesInterface $properties)
    {
        $this->source = $source;
        $this->callback = $callback;
        $this->properties = $properties;
    }

    public function __invoke(
        NodeValueInterface $rootNode,
        RuntimeInterface $runtime,
        EvaluatorInterface $evaluator
    ): ValueListInterface {
        $input = (new NodeValueListBuilder)
            ->addValue($rootNode, 0)
            ->build();

        return call_user_func($this->callback, $input, $runtime, $evaluator);
    }

    public function getCapabilities(): CapabilitiesInterface
    {
        return $this->properties;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
