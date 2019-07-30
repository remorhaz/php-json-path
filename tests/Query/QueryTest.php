<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Query\Query;
use Remorhaz\JSON\Path\Query\QueryInterface;
use Remorhaz\JSON\Path\Query\CapabilitiesInterface;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

/**
 * @covers \Remorhaz\JSON\Path\Query\Query
 */
class QueryTest extends TestCase
{

    public function testInvoke_ConstructedWithCallback_CallsSameCallback(): void
    {
        $rootValue = $this->createMock(NodeValueInterface::class);
        $runtime = $this->createMock(RuntimeInterface::class);
        $evaluator = $this->createMock(EvaluatorInterface::class);
        $isCallbackCalledWithMatchingArgs = null;
        $callback = function (
            NodeValueListInterface $inputArg,
            RuntimeInterface $runtimeArg,
            EvaluatorInterface $evaluatorArg
        ) use (
            $rootValue,
            $runtime,
            $evaluator,
            &$isCallbackCalledWithMatchingArgs
        ): ValueListInterface {
            $isCallbackCalledWithMatchingArgs =
                $inputArg->getValues() === [$rootValue] &&
                $runtimeArg === $runtime &&
                $evaluatorArg === $evaluator;

            return $this->createMock(ValueListInterface::class);
        };

        $query = new Query(
            'a',
            $callback,
            $this->createMock(CapabilitiesInterface::class)
        );

        $query($rootValue, $runtime, $evaluator);
        self::assertTrue($isCallbackCalledWithMatchingArgs);
    }

    public function testInvoke_CallbackReturnsValueList_ReturnsSameInstance(): void
    {
        $values = $this->createMock(ValueListInterface::class);
        $callback = function () use ($values): ValueListInterface {
            return $values;
        };
        $query = new Query(
            'a',
            $callback,
            $this->createMock(CapabilitiesInterface::class)
        );

        $actualValue = $query(
            $this->createMock(NodeValueInterface::class),
            $this->createMock(RuntimeInterface::class),
            $this->createMock(EvaluatorInterface::class),
        );
        self::assertSame($values, $actualValue);
    }

    public function testGetProperties_ConstructedWithGivenProperties_ReturnsSameInstance(): void
    {
        $properties = $this->createMock(CapabilitiesInterface::class);
        $callback = $this->createMock(QueryInterface::class);
        $query = new Query('a', $callback, $properties);

        self::assertSame($properties, $query->getCapabilities());
    }

    public function providerIsDefinite(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }

    public function testGetSource_ConstructedWithGivenSource_ReturnsSameValue(): void
    {
        $query = new Query(
            'a',
            $this->createMock(QueryInterface::class),
            $this->createMock(CapabilitiesInterface::class)
        );
        self::assertSame('a', $query->getSource());
    }
}
