<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use Closure;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Query\Exception\CapabilitiesNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\QueryCallbackNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\ReferenceNotFoundException;
use Remorhaz\JSON\Path\Query\CallbackBuilder;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

/**
 * @covers \Remorhaz\JSON\Path\Query\CallbackBuilder
 */
class CallbackBuilderTest extends TestCase
{

    public function testGetQueryCallback_CallbackIsNotSet_ThrowsException(): void
    {
        $callbackBuilder = new CallbackBuilder;

        $this->expectException(QueryCallbackNotFoundException::class);
        $callbackBuilder->getQueryCallback();
    }

    public function testGetQueryCallback_OnFinishCalled_ReturnsClosureInstance(): void
    {
        $callbackBuilder = new CallbackBuilder;
        $callbackBuilder->onFinish();
        self::assertInstanceOf(Closure::class, $callbackBuilder->getQueryCallback());
    }

    public function testGetQueryCapabilities_QueryCapabilitiesNotSet_ThrowsException(): void
    {
        $callbackBuilder = new CallbackBuilder;

        $this->expectException(CapabilitiesNotFoundException::class);
        $callbackBuilder->getQueryCapabilities();
    }

    /**
     * @param bool $isDefinite
     * @param bool $expectedValue
     * @throws UniLexException
     * @dataProvider providerIsDefinite
     */
    public function testGetCapabilities_SetOutputProductionFinishedWithGivenCapabilities_ReturnsMatchingCapabilities(
        bool $isDefinite,
        bool $expectedValue
    ): void {
        $callbackBuilder = new CallbackBuilder;
        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $callbackBuilder->onFinishProduction($inputNode);

        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode
            ->setAttribute('is_definite', $isDefinite)
            ->setAttribute('is_path', false)
            ->addChild($inputNode);

        $callbackBuilder->onFinishProduction($setOutputNode);

        self::assertSame($expectedValue, $callbackBuilder->getQueryCapabilities()->isDefinite());
    }

    public function providerIsDefinite(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }

    public function testOnBeginProduction_QueryAstNodeWithChildren_PushesReversedChildrenInStack(): void
    {
        $callbackBuilder = new CallbackBuilder;

        $node = new Node(1, 'a');
        $firstChild = new Node(2, 'b');
        $secondChild = new Node(3, 'c');
        $node->addChild($firstChild);
        $node->addChild($secondChild);

        $stack = $this->createMock(PushInterface::class);
        $stack
            ->expects(self::once())
            ->method('push')
            ->with($secondChild, $firstChild);
        $callbackBuilder->onBeginProduction($node, $stack);
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_SetOutputWithChildWithoutReference_ThrowsException(): void
    {
        $callbackBuilder = new CallbackBuilder;
        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode
            ->setAttribute('is_definite', true)
            ->setAttribute('is_path', false)
            ->addChild($inputNode);

        $this->expectException(ReferenceNotFoundException::class);
        $callbackBuilder->onFinishProduction($setOutputNode);
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_SetOutputWithInputChild_CallbackPassesRootValueToRuntime(): void
    {
        $callbackBuilder = new CallbackBuilder;
        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $callbackBuilder->onFinishProduction($inputNode);

        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode
            ->setAttribute('is_definite', true)
            ->setAttribute('is_path', false)
            ->addChild($inputNode);

        $callbackBuilder->onFinishProduction($setOutputNode);

        $callbackBuilder->onFinish();

        $rootValue = $this->createMock(NodeValueInterface::class);
        $runtime = $this->createMock(RuntimeInterface::class);
        $callback = $callbackBuilder->getQueryCallback();

        $runtime
            ->expects(self::once())
            ->method('getInput')
            ->with($rootValue);
        $callback($runtime, $rootValue);
    }

    /**
     * @throws UniLexException
     */
    public function testOnFinishProduction_SetOutputWithInputChild_CallbackReturnsValueListFromRuntime(): void
    {
        $callbackBuilder = new CallbackBuilder;
        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $callbackBuilder->onFinishProduction($inputNode);

        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode
            ->setAttribute('is_definite', true)
            ->setAttribute('is_path', false)
            ->addChild($inputNode);

        $callbackBuilder->onFinishProduction($setOutputNode);

        $callbackBuilder->onFinish();

        $rootValue = $this->createMock(NodeValueInterface::class);
        $runtime = $this->createMock(RuntimeInterface::class);
        $callback = $callbackBuilder->getQueryCallback();

        $values = $this->createMock(NodeValueListInterface::class);
        $runtime
            ->method('getInput')
            ->willReturn($values);
        $actualValue = $callback($runtime, $rootValue);
        self::assertSame($values, $actualValue);
    }
}
