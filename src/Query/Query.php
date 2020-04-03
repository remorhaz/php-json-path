<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Throwable;

use function call_user_func;

final class Query implements QueryInterface
{

    private $source;

    private $callbackBuilder;

    public function __construct(string $source, CallbackBuilderInterface $callbackBuilder)
    {
        $this->source = $source;
        $this->callbackBuilder = $callbackBuilder;
    }

    public function __invoke(NodeValueInterface $rootNode, RuntimeInterface $runtime): ValueListInterface
    {
        try {
            $callback = $this
                ->callbackBuilder
                ->getCallback();

            $args = [
                (new NodeValueListBuilder())
                    ->addValue($rootNode, 0)
                    ->build(),
                $runtime->getValueListFetcher(),
                $runtime->getEvaluator(),
                $runtime->getLiteralFactory(),
                $runtime->getMatcherFactory(),
            ];

            return call_user_func($callback, ...$args);
        } catch (Throwable $e) {
            throw new Exception\QueryExecutionFailedException(
                $this->source,
                $this->callbackBuilder->getCallbackCode(),
                $e,
            );
        }
    }

    public function getCapabilities(): CapabilitiesInterface
    {
        return $this
            ->callbackBuilder
            ->getCapabilities();
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
