<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\CapabilitiesNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\QueryCallbackCodeNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\ReferenceNotFoundException;
use Remorhaz\JSON\Path\Query\CallbackBuilder;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

/**
 * @covers \Remorhaz\JSON\Path\Query\CallbackBuilder
 */
class CallbackBuilderTest extends TestCase
{

    public function testGetCallbackCode_CallbackIsNotSet_ThrowsException(): void
    {
        $callbackBuilder = new CallbackBuilder;

        $this->expectException(QueryCallbackCodeNotFoundException::class);
        $callbackBuilder->getCallbackCode();
    }

    public function testGetCapabilities_QueryCapabilitiesNotSet_ThrowsException(): void
    {
        $callbackBuilder = new CallbackBuilder;

        $this->expectException(CapabilitiesNotFoundException::class);
        $callbackBuilder->getCapabilities();
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

        self::assertSame($expectedValue, $callbackBuilder->getCapabilities()->isDefinite());
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
}
