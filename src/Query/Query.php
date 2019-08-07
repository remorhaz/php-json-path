<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Value\NodeValueListBuilder;
use Throwable;
use function call_user_func;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;

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
            $input = (new NodeValueListBuilder)
                ->addValue($rootNode, 0)
                ->build();
            $callback = $this
                ->callbackBuilder
                ->getCallback();

            return call_user_func(
                $callback,
                $input,
                $runtime->getValueListFetcher(),
                $runtime->getEvaluator(),
                $runtime->getLiteralFactory(),
                $runtime->getMatcherFactory(),
            );
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
