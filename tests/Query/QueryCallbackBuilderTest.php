<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use Closure;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\Exception\IsDefiniteFlagNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\QueryCallbackNotFoundException;
use Remorhaz\JSON\Path\Query\Exception\ReferenceNotFoundException;
use Remorhaz\JSON\Path\Query\QueryCallbackBuilder;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

/**
 * @covers \Remorhaz\JSON\Path\Query\QueryCallbackBuilder
 */
class QueryCallbackBuilderTest extends TestCase
{

    public function testGetQueryCallback_CallbackIsNotSet_ThrowsException(): void
    {
        $callbackBuilder = new QueryCallbackBuilder;

        $this->expectException(QueryCallbackNotFoundException::class);
        $callbackBuilder->getQueryCallback();
    }

    public function testGetQueryCallback_OnFinishCalled_ReturnsClosureInstance(): void
    {
        $callbackBuilder = new QueryCallbackBuilder;
        $callbackBuilder->onFinish();
        self::assertInstanceOf(Closure::class, $callbackBuilder->getQueryCallback());
    }

    public function testIsDefinite_IsDefiniteIsNotSet_ThrowsException(): void
    {
        $callbackBuilder = new QueryCallbackBuilder;

        $this->expectException(IsDefiniteFlagNotFoundException::class);
        $callbackBuilder->isDefinite();
    }

    /**
     * @param bool $isDefinite
     * @param bool $expectedValue
     * @throws UniLexException
     * @dataProvider providerIsDefinite
     */
    public function testIsDefinite_SetOutputProductionFinishedWithGivenIsDefinite_ReturnsSameValue(
        bool $isDefinite,
        bool $expectedValue
    ): void {
        $callbackBuilder = new QueryCallbackBuilder;

        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $callbackBuilder->onFinishProduction($inputNode);

        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode->setAttribute('is_definite', $isDefinite);
        $setOutputNode->addChild($inputNode);

        $callbackBuilder->onFinishProduction($setOutputNode);

        self::assertSame($expectedValue, $callbackBuilder->isDefinite());
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
        $callbackBuilder = new QueryCallbackBuilder;

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
    public function testOnFinishProduction_SetOutputChildWithoutReference_ThrowsException(): void
    {
        $callbackBuilder = new QueryCallbackBuilder;

        $callbackBuilder->onStart($this->createMock(Node::class));

        $inputNode = new Node(1, 'get_input');
        $setOutputNode = new Node(2, 'set_output');
        $setOutputNode->setAttribute('is_definite', true);
        $setOutputNode->addChild($inputNode);

        $this->expectException(ReferenceNotFoundException::class);
        $callbackBuilder->onFinishProduction($setOutputNode);
    }
}